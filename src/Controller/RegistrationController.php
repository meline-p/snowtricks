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

/**
 * Controller class responsible for handling registration actions.
 */
class RegistrationController extends AbstractController
{
    private $slugger;

    public function __construct(
        SluggerInterface $slugger,
    ) {
        $this->slugger = $slugger;
    }

    /**
     * Handles the user registration process.
     *
     * @param Request                     $request            the HTTP request object containing form data and other request information
     * @param UserPasswordHasherInterface $userPasswordHasher the service used to hash the user's password
     * @param Security                    $security           the security service used for user authentication
     * @param EntityManagerInterface      $entityManager      the service used to manage database operations
     * @param SendMailService             $mail               the service used for sending emails
     * @param JWTService                  $jwt                the service used for generating JWT tokens
     *
     * @return Response a Response object that renders the registration form view or handles user login and redirection
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
     * Verifies a user's account based on a JWT token.
     *
     * @param string                 $token          the JWT token used for verifying the user's account
     * @param JWTService             $jwt            the service used for handling JWT operations such as validation and decoding
     * @param UserRepository         $userRepository the repository used to fetch user data from the database
     * @param EntityManagerInterface $em             the service used for managing database operations
     *
     * @return Response a Response object that redirects to the profile page or login page with a flash message
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
     * Resends the verification email to the currently logged-in user.
     *
     * @param JWTService      $jwt            the service used for handling JWT operations, such as token generation
     * @param SendMailService $mail           the service used for sending emails
     * @param UserRepository  $userRepository the repository used to fetch user data from the database
     *
     * @return Response a Response object that redirects the user to their profile page or login page with flash messages
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
