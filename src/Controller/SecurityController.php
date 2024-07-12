<?php

namespace App\Controller;

use App\Form\ResetPasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\UserRepository;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * Handles the forgotten password request.
     */
    #[Route(path: '/forgot-password', name: 'app_forgotten_password')]
    public function forgottenPassword(
        Request $request,
        UserRepository $userRepository,
        TokenGeneratorInterface $tokenGenerator,
        EntityManagerInterface $entityManager,
        SendMailService $mail
    ): Response {
        $form = $this->createForm(ResetPasswordRequestFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check if user exists
            $user = $userRepository->findOneByEmail($form->get('email')->getData());

            if (!$user) {
                $this->addFlash('danger', 'Erreur : Aucun compte associé à cette adresse e-mail. Veuillez vérifier votre saisie ou créer un nouveau compte si nécessaire.');

                return $this->redirectToRoute('app_login');
            }

            // Generate a reset token
            $token = $tokenGenerator->generateToken();
            $user->setResetToken($token);
            $entityManager->persist($user);
            $entityManager->flush();

            // Generate password reset URL
            $url = $this->generateUrl('app_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

            // Prepare mail data
            $context = compact('url', 'user');

            // Send the mail
            $mail->send(
                'no-reply@snowtricks.fr',
                $user->getEmail(),
                'Réinitialisation de mot de passe',
                'password_reset',
                $context
            );

            $this->addFlash('success', 'Email envoyé avec succès');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password_request.html.twig', [
            'requestPassForm' => $form->createView(),
        ]);
    }

    /**
     * Resets user password.
     */
    #[Route(path: '/forgot-password/{token}', name: 'app_reset_password')]
    public function resetPassword(
        string $token,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        // Check if token exists in database
        $user = $userRepository->findOneByResetToken($token);

        if (!$user) {
            $this->addFlash('danger', 'Jeton invalide');

            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ResetPasswordFormType::class);

        $oldPassword = null;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Clear the reset token
            $user->setResetToken('');
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Mot de passe changé avec succès');

            return $this->redirectToRoute('app_login');
        }

        if ($form->isSubmitted()) {
            $oldPassword = $form->get('password')->getData();
        }

        return $this->render('security/reset_password.html.twig', [
            'passForm' => $form->createView(),
            'oldPassword' => $oldPassword,
        ]);
    }
}
