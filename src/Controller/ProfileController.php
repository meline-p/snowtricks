<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trait\ProcessImageTrait;
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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Controller class responsible for handling profile-related actions.
 */
#[Route('/profil', name: 'app_profile_')]
class ProfileController extends AbstractController
{
    use ProcessImageTrait;

    /**
     * Displays the user's profile.
     *
     * @return Response a response object containing the rendered profile view
     */
    #[Route('/profil/{user_username}', name: 'index')]
    public function index(
        string $user_username, 
        UserTrickRepository $userTrickRepository, 
        CommentRepository $commentRepository,
        UserRepository $userRepository,
        Request $request,
        EntityManagerInterface $em,
        PictureService $pictureService
        ): Response
    {
        // Retrieve the currently logged-in user
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        // Retrieve the user whose profile is being viewed
        $user = $userRepository->findOneBy(['username' => $user_username]);

        // Check if the currently logged-in user is the one whose profile is being viewed
        if ($currentUser !== $user) {
            $this->addFlash('danger', 'Vous ne pouvez accéder qu\'à votre propre profil.');
            return $this->redirectToRoute('app_main');
        }

        // Count the tricks created, updated, and the comments left by the user
        $countTricksCreated = $userTrickRepository->count(['user'=> $user, 'operation' => 'create']);
        $countTricksUpdated = $userTrickRepository->count(['user' => $user, 'operation' => 'update']);
        $countComments = $commentRepository->count(['user' => $user]);

        // comments
        $profilPicture = new Image();

        $profilPictureForm = $this->createForm(ProfilePictureFormType::class, $profilPicture);
        $profilPictureForm->handleRequest($request);

        if ($profilPictureForm->isSubmitted() && $profilPictureForm->isValid()) {
            
            $em->getConnection()->beginTransaction();

            try{
                // remove current profile picture
                $filesystem = new Filesystem();
                $currentPictureProfile = $user->getPictureSlug();
                $pathToPicture = 'assets/img/users/' . $currentPictureProfile;

                if ($filesystem->exists($pathToPicture)) {
                    $filesystem->remove($pathToPicture);
                } 
                        
                // set the new profil picture
                $profilPicture = $profilPictureForm->get('profilPicture')->getData();
                $folder = "users";
                $profilPicture = $this->processProfilPicture($profilPicture, $user, $pictureService, $folder);

                $em->persist($user);
                $em->flush();

                $em->getConnection()->commit();

            }catch (\Exception $e) {
                $em->getConnection()->rollBack();
                throw $e;
            }

            $this->addFlash('success', 'Photo de profil modifiée avec succès');

            $route = $request->headers->get('referer');
            return $this->redirect($route);
            
        }

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'countTricksCreated' => $countTricksCreated,
            'countTricksUpdated' => $countTricksUpdated,
            'countComments' => $countComments,
            'profilPictureForm' => $profilPictureForm->createView()
        ]);
    }

    #[Route('/edit-infos/{user_username}', name: 'edit_infos')]
    public function editInfos(
        string $user_username,
        UserRepository $userRepository,
        Request $request,
        EntityManagerInterface $em
        ){

        $this->denyAccessUnlessGranted('ROLE_USER');

        // Retrieve the currently logged-in user
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        // Retrieve the user whose profile is being viewed
        $user = $userRepository->findOneBy(['username' => $user_username]);

        // Check if the currently logged-in user is the one whose profile is being viewed
        if ($currentUser !== $user) {
            $this->addFlash('danger', 'Vous ne pouvez accéder qu\'à votre propre profil.');
            return $this->redirectToRoute('app_main');
        }

        $form = $this->createForm(ProfileInfosFormType::class, $currentUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Vos informations ont été mise à jour.');
            return $this->redirectToRoute('app_profile_index', ['user_username' => $user->getUsername()]);
        }

        return $this->render('profile/edit_infos.html.twig', [
            'editInfosForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/edit-password/{user_username}', name: 'edit_password')] 
    public function editPassword(
        string $user_username,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {

        $this->denyAccessUnlessGranted('ROLE_USER');

        // Retrieve the currently logged-in user
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        // Retrieve the user whose profile is being viewed
        $user = $userRepository->findOneBy(['username' => $user_username]);

        // Check if the currently logged-in user is the one whose profile is being viewed
        if ($currentUser !== $user) {
            $this->addFlash('danger', 'Vous ne pouvez accéder qu\'à votre propre profil.');
            return $this->redirectToRoute('app_main');
        }

        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // Clear the reset token
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user, 
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Mot de passe modifié avec succès');

            return $this->redirectToRoute('app_profile_index', ['user_username' => $user->getUsername()]);
        }

        return $this->render('security/reset_password.html.twig', [
            'passForm' => $form->createView()
        ]);
    }

}
