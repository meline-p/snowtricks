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

/**
 * Controller class responsible for handling log in, log out, and password reset actions.
 */
class SecurityController extends AbstractController
{
    /**
     * Displays the login page and handles login errors.
     *
     * @param AuthenticationUtils $authenticationUtils the service used to get information about the last authentication attempt
     *
     * @return Response a Response object that renders the login page with any authentication errors and the last entered username
     */
    #[Route(path: '/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * Handles user logout.
     *
     * @return void this method does not return a response as the logout logic is handled by Symfony's security system
     *
     * @throws \LogicException this exception is thrown to indicate that the method is intentionally left blank and that
     *                         Symfony's security system will handle the logout process
     */
    #[Route(path: '/deconnexion', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * Handles the password reset request process.
     *
     * @param Request                 $request        the HTTP request object containing the form submission data
     * @param UserRepository          $userRepository the repository to fetch user information
     * @param TokenGeneratorInterface $tokenGenerator the service used to generate a unique reset token
     * @param EntityManagerInterface  $entityManager  the entity manager to persist changes to the database
     * @param SendMailService         $mail           the service used to send emails
     *
     * @return Response a Response object that either renders the password reset request form or redirects to the login page
     */
    #[Route(path: '/mot-de-passe-oublie', name: 'app_forgotten_password')]
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
     * Handles the password reset process.
     *
     * @param string                      $token          the password reset token used to identify the user
     * @param Request                     $request        the HTTP request object containing the form submission data
     * @param UserRepository              $userRepository the repository used to fetch user information by reset token
     * @param EntityManagerInterface      $entityManager  the entity manager used to persist changes to the database
     * @param UserPasswordHasherInterface $passwordHasher the service used to hash the new password
     *
     * @return Response a Response object that either renders the password reset form or redirects to the login page
     */
    #[Route(path: '/mot-de-passe-oublie/{token}', name: 'app_reset_password')]
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
