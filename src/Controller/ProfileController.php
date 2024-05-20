<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trait\ProcessImageTrait;
use App\Entity\User;
use App\Form\ProfilePictureFormType;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use App\Repository\UserTrickRepository;
use App\Service\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
            
            $profilPicture = $profilPictureForm->get('profilPicture')->getData();

            $folder = "users";

            $profilPicture = $this->processProfilPicture($profilPicture, $user, $pictureService, $folder);

            $em->persist($user);
            $em->flush();

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
}
