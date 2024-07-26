<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\User;
use App\Form\ProfileInfosFormType;
use App\Form\ProfilePictureFormType;
use App\Form\ResetPasswordFormType;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use App\Repository\UserTrickRepository;
use App\Service\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Controller class responsible for handling profile-related actions.
 */
#[Route('/profil', name: 'app_profile_')]
class ProfileController extends AbstractController
{
    private $slugger;
    private $em;
    private $pictureService;
    private $userTrickRepository;
    private $commentRepository;
    private $userRepository;

    public function __construct(
        SluggerInterface $slugger,
        EntityManagerInterface $em,
        PictureService $pictureService,
        UserTrickRepository $userTrickRepository,
        CommentRepository $commentRepository,
        UserRepository $userRepository,
    ) {
        $this->slugger = $slugger;
        $this->em = $em;
        $this->userTrickRepository = $userTrickRepository;
        $this->commentRepository = $commentRepository;
        $this->userRepository = $userRepository;
        $this->pictureService = $pictureService;
    }

    /**
     * Retrieves a user by their username if the current user is the same.
     *
     * @param string $username the username of the user to retrieve
     *
     * @return User|null the `User` entity if the current user matches the fetched user, otherwise `null`
     */
    public function getUserByUsername(string $username): ?User
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $user = $this->userRepository->findOneBy(['username' => $username]);

        if ($currentUser !== $user) {
            return null;
        }

        return $user;
    }

    /**
     * Retrieves statistics for a specific user.
     *
     * @param User $user the `User` entity for which statistics are to be retrieved
     *
     * @return array An associative array containing the statistics:
     *               - 'tricksCreated': Number of tricks created by the user.
     *               - 'tricksUpdated': Number of tricks updated by the user.
     *               - 'comments': Number of comments made by the user.
     */
    private function getStatistics(User $user): array
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $countTricksCreated = $this->userTrickRepository->count(['user' => $user, 'operation' => 'create']);
        $countTricksUpdated = $this->userTrickRepository->count(['user' => $user, 'operation' => 'update']);
        $countComments = $this->commentRepository->count(['user' => $user]);

        return [
            'tricksCreated' => $countTricksCreated,
            'tricksUpdated' => $countTricksUpdated,
            'comments' => $countComments,
        ];
    }

    /**
     * Displays the profile page for a specific user.
     *
     * @param string  $user_username the username of the user whose profile is to be displayed
     * @param Request $request       the HTTP request object containing form data and other request information
     *
     * @return Response a Response object that renders the profile page view with the user's data and form
     */
    #[Route('/{user_username}', name: 'index', requirements: ['user_username' => '[a-z0-9\-]+'])]
    public function index(string $user_username, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUserByUsername($user_username);
        if (null === $user) {
            $this->addFlash('danger', 'Vous ne pouvez accéder qu\'à votre propre profil.');

            return $this->redirectToRoute('app_main');
        }

        $statistics = $this->getStatistics($user);

        // update profile picture
        $profilPictureForm = $this->createForm(ProfilePictureFormType::class, new Image());
        $profilPictureForm->handleRequest($request);

        if ($profilPictureForm->isSubmitted() && $profilPictureForm->isValid()) {
            $folder = 'users';
            $newImg = $profilPictureForm->get('profilPicture')->getData();

            $success = $this->pictureService->setNewProfilePicture($user, $newImg, $folder);

            if ($success) {
                $this->addFlash('success', 'Photo de profil modifiée avec succès');
            } else {
                $this->addFlash('danger', 'Erreur : '.$newImg->getClientOriginalName()." : Format d'image incorrect.");
            }

            $route = $request->headers->get('referer');
            return $this->redirect($route);
        }

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'countTricksCreated' => $statistics['tricksCreated'],
            'countTricksUpdated' => $statistics['tricksUpdated'],
            'countComments' => $statistics['comments'],
            'profilPictureForm' => $profilPictureForm->createView(),
        ]);
    }

    /**
     * Handles the update of a user's profile information.
     *
     * @param string  $user_username the username of the user whose information is being edited
     * @param Request $request       the HTTP request object containing form data and other request information
     *
     * @return Response a Response object that renders the form view or redirects based on the form submission
     */
    #[Route('/modifier-mes-infos/{user_username}', name: 'edit_infos', requirements: ['user_username' => '[a-z0-9\-]+'])]
    public function editInfos(string $user_username, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUserByUsername($user_username);
        if (null === $user) {
            $this->addFlash('danger', 'Vous ne pouvez accéder qu\'à votre propre profil.');

            return $this->redirectToRoute('app_main');
        }

        $form = $this->createForm(ProfileInfosFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUsername(strtolower($this->slugger->slug($user->getUsername())));
            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', 'Vos informations ont été mise à jour.');

            return $this->redirectToRoute('app_profile_index', ['user_username' => $user->getUsername()]);
        }

        return $this->render('profile/edit_infos.html.twig', [
            'editInfosForm' => $form->createView(),
        ]);
    }

    /**
     * Handles the password update process for a user.
     *
     * @param string                      $user_username  the username of the user whose password is being changed
     * @param Request                     $request        the HTTP request object containing form data and other request information
     * @param UserPasswordHasherInterface $passwordHasher the service used to hash the new password
     *
     * @return Response a Response object that renders the password reset form view or redirects based on the form submission
     */
    #[Route(path: '/modifier-mot-de-passe/{user_username}', name: 'edit_password', requirements: ['user_username' => '[a-z0-9\-]+'])]
    public function editPassword(
        string $user_username,
        Request $request,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUserByUsername($user_username);
        if (null === $user) {
            $this->addFlash('danger', 'Vous ne pouvez accéder qu\'à votre propre profil.');

            return $this->redirectToRoute('app_main');
        }

        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);

        $oldPassword = null;

        if ($form->isSubmitted() && $form->isValid()) {
            // Clear the reset token
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', 'Mot de passe modifié avec succès');

            return $this->redirectToRoute('app_profile_index', ['user_username' => $user->getUsername()]);
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
