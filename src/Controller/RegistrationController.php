<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class RegistrationController extends AbstractController
{

    private $slugger;

    public function __construct(
        SluggerInterface $slugger,
    ) {
        $this->slugger = $slugger;
    }

    /**
     * Handles user registration.
     */
    #[Route('/inscription', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        Security $security,
        EntityManagerInterface $entityManager,
        SendMailService $mail,
        JWTService $jwt
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        $plainPassword = null;

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setLastName(strtoupper($user->getLastName()));
            $user->setFirstName(ucfirst(strtolower($user->getFirstName())));
            $user->setUsername(strtolower($this->slugger->slug($user->getUsername())));

            $entityManager->persist($user);
            $entityManager->flush();

            // Generate JWT for the user
            // Create JWT header
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256',
            ];

            // Create JWT payload
            $payload = [
                'user_id' => $user->getId(),
            ];

            // Generate JWT token
            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            // Send activation email
            $mail->send(
                'no-reply@snowtricks.com',
                $user->getEmail(),
                'Activation de votre compte sur Snowtricks',
                'register',
                compact('user', 'token')
            );

            return $security->login($user, UserAuthenticator::class, 'main');
        }

        if ($form->isSubmitted()) {
            $plainPassword = $form->get('plainPassword')->getData();
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
            'plainPassword' => $plainPassword,
        ]);
    }

    /**
     * Verifies user account using provided token.
     */
    #[Route('/verification/{token}', name: 'app_verify_user')]
    public function verifyUser(string $token, JWTService $jwt, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        // Check if the token is valid, not expired, and not modified
        if ($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))) {
            // Get the payload from the token
            $payload = $jwt->getPayload($token);

            // Find the user associated with the token
            $user = $userRepository->find($payload['user_id']);

            // Check if the user exists and hasn't activated their account yet
            if ($user && !$user->getIsVerified()) {
                $user->setIsVerified(true);
                $em->flush($user);
            }

            $this->addFlash('success', 'Votre compte est bien activé');

            return $this->redirectToRoute('app_profile_index', ['user_username' => $user->getUsername()]);
        }

        $this->addFlash('danger', 'Le token est invalide ou a expiré');

        return $this->redirectToRoute('app_login');
    }

    /**
     * Resends verification email to the current user.
     */
    #[Route('/renvoi-verification', name: 'app_resend_verif')]
    public function resendVerif(JWTService $jwt, SendMailService $mail, UserRepository $userRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour accéder à cette page');

            return $this->redirectToRoute('app_login');
        }

        if ($user->getIsVerified()) {
            $this->addFlash('warning', 'Cet utilisateur est déjà activé');

            return $this->redirectToRoute('app_profile_index', ['user_username' => $user->getUsername()]);
        }

        // Generate JWT for the user
        // Create JWT header
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256',
        ];

        // Create JWT payload
        $payload = [
            'user_id' => $user->getId(),
        ];

        // Generate JWT token
        $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

        // Send verification email
        $mail->send(
            'no-reply@snowtricks.com',
            $user->getEmail(),
            'Activation de votre compte sur Snowtricks',
            'register',
            compact('user', 'token')
        );

        $this->addFlash('success', 'Email de vérification envoyé');

        return $this->redirectToRoute('app_profile_index', ['user_username' => $user->getUsername()]);
    }
}
