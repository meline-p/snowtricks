<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\UserTrick;
use App\Form\CommentsFormType;
use App\Form\DeleteTrickType;
use App\Form\TricksFormType;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use App\Repository\UserTrickRepository;
use App\Repository\VideoRepository;
use App\Service\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/tricks', name: 'app_tricks_')]
class TricksController extends AbstractController
{
    private $slugger;
    private $em;
    private $pictureService;
    private $categoryRepository;
    private $trickRepository;
    private $imageRepository;
    private $videoRepository;
    private $userTrickRepository;
    private $commentRepository;

    public function __construct(
        SluggerInterface $slugger, 
        EntityManagerInterface $em, 
        PictureService $pictureService,
        CategoryRepository $categoryRepository,
        TrickRepository $trickRepository,
        ImageRepository $imageRepository,
        VideoRepository $videoRepository,
        UserTrickRepository $userTrickRepository,
        CommentRepository $commentRepository,
        )
    {
        $this->slugger = $slugger;
        $this->em = $em;
        $this->pictureService = $pictureService;
        $this->categoryRepository = $categoryRepository;
        $this->trickRepository = $trickRepository;
        $this->imageRepository = $imageRepository;
        $this->videoRepository = $videoRepository;
        $this->userTrickRepository = $userTrickRepository;
        $this->commentRepository = $commentRepository;
    }

    #[Route('/categories/{category_slug}', name: 'index')]
    public function index(?string $category_slug,Request $request): Response {
        // Check if the category_slug parameter is present
        if ('all' === $category_slug) {
            $tricks = $this->trickRepository->findAll();
            $category_slug = 'all';
        } else {
            $categorySelected = $this->categoryRepository->findOneBy(['slug' => $category_slug]);
            $category_slug = $category_slug;
            $tricks = $this->trickRepository->findByCategory($categorySelected);
        }
        $categories = $this->categoryRepository->findAll();

        // Get the page number from the URL query parameters
        $page = $request->query->getInt('page', 1);

        // Retrieve paginated tricks based on the selected category
        $tricks = $this->trickRepository->findTricksPaginated($page, $category_slug, 6);

        $deleteForms = [];
        if (count($tricks) > 0) {
            foreach ($tricks['data'] as $trick) {
                $deleteForms[$trick->getId()] = $this->createForm(DeleteTrickType::class)->createView();
            }
        }

        return $this->render('tricks/index.html.twig', [
            'categories' => $this->categoryRepository->findBy([], ['categoryOrder' => 'asc']),
            'tricks' => $tricks,
            'categories' => $categories,
            'category_slug' => $category_slug,
            'deleteForms' => $deleteForms,
        ]);
    }

    #[Route('/details/{slug}', name: 'details')]
    public function details(Trick $trick,Request $request): Response {
        $images = $this->imageRepository->findBy(['trick' => $trick]);
        $videos = $this->videoRepository->findBy(['trick' => $trick]);
        $comments = $this->commentRepository->findBy(['trick' => $trick], ['created_at' => 'DESC']);

        // Get the page number from the URL query parameters
        $page = $request->query->getInt('page', 1);
        $comments = $this->commentRepository->findCommentsPaginated($page, $trick->getId(), 3);

        $userTrickCreatedAt = $this->userTrickRepository->findOneBy(['trick' => $trick, 'operation' => 'create']);
        $created_at = $userTrickCreatedAt ? $userTrickCreatedAt->getDate() : null;

        $userTrickUpdatedAt = $this->userTrickRepository->findOneBy(['trick' => $trick, 'operation' => 'update'], ['date' => 'DESC']);
        $updated_at = $userTrickUpdatedAt ? $userTrickUpdatedAt->getDate() : null;

        $deleteForms[$trick->getId()] = $this->createForm(DeleteTrickType::class)->createView();

        // comments
        $comment = new Comment();

        /** @var User $user */
        $user = $this->getUser();

        $commentForm = $this->createForm(CommentsFormType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setContent($comment->getContent());
            $comment->setUser($user);
            $comment->setTrick($trick);

            $this->em->persist($comment);
            $this->em->flush();

            $route = $request->headers->get('referer');

            return $this->redirect($route.'#comments');
        }

        return $this->render('tricks/details.html.twig', [
            'trick' => $trick,
            'images' => $images,
            'videos' => $videos,
            'comments' => $comments,
            'created_at' => $created_at,
            'updated_at' => $updated_at,
            'deleteForms' => $deleteForms,
            'commentForm' => $commentForm->createView(),
        ]);
    }

    public function handleSubmittedForms(
        Trick $trick,
        User $user,
        $trickForm,
        $operation
    ) {
        $this->em->getConnection()->beginTransaction();

        try {

            // ----- TRICK -------
            // Generate slug for the trick's name
            $slug = strtolower($this->slugger->slug($trick->getName()));
            $trick->setSlug($slug);

            $description = $trick->getDescription();
            $trick->setDescription($description);

            // ----- IMAGES -------
            // Retrieve the images
            $images = $trickForm->get('images')->getData();

            // Define the destination folder
            $folder = 'tricks';

            foreach ($images as $image) {
                $img = $this->pictureService->processImage($image, $folder);
                $trick->addImage($img);
            }

            // ----- PROMOTE IMAGE -------
            // Retrive the promote image
            $promoteImage = $trickForm->get('promoteImage')->getData();

            if ($promoteImage) {
                $promoteImg = $this->pictureService->processImage($promoteImage, $folder);
                $trick->addImage($promoteImg);
                $trick->setPromoteImage($promoteImg);
            }

            // Persist the trick with all its changes (including images)
            $this->em->persist($trick);

            // ----- USERTRICKS -------
            $userTrick = new UserTrick();
            $userTrick->setOperation($operation);
            $userTrick->setDate(new \DateTime());
            $userTrick->setUser($user);
            $userTrick->setTrick($trick);

            $this->em->persist($userTrick);

            // Flush all changes at once
            $this->em->flush();

            $this->em->getConnection()->commit();

            return $slug;
        } catch (\Exception $e) {
            // Rollback the transaction
            $this->em->getConnection()->rollBack();
            throw $e;
            $this->addFlash('danger', 'Une erreur a eu lieu lors de la soumission du formulaire.');
        }
    }

    #[Route('/ajouter', name: 'add')]
    public function add(Request $request): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Create new instances of Trick
        $trick = new Trick();

        /** @var User $user */
        $user = $this->getUser();

        // Create the form for adding a trick
        $trickForm = $this->createForm(TricksFormType::class, $trick);

        // Handle form submission
        $trickForm->handleRequest($request);

        // Check if form is submitted and valid
        if ($trickForm->isSubmitted() && $trickForm->isValid()) {
            $slug = $this->handleSubmittedForms($trick, $user, $trickForm, 'create');

            $this->addFlash('success', 'Figure ajoutée avec succès');

            return $this->redirectToRoute('app_tricks_details', ['slug' => $slug]);
        }

        return $this->render('tricks/add.html.twig', [
            'trickForm' => $trickForm->createView(),
        ]);
    }

    #[Route('/modifier/{slug}', name: 'edit')]
    public function edit(Trick $trick,Request $request): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $images = $this->imageRepository->findBy(['trick' => $trick]);
        $videos = $this->videoRepository->findBy(['trick' => $trick]);

        /** @var User $user */
        $user = $this->getUser();

        // Create the form for editing the trick
        $trickForm = $this->createForm(TricksFormType::class, $trick);

        // Handle form submission
        $trickForm->handleRequest($request);

        // Check if form is submitted and valid
        if ($trickForm->isSubmitted() && $trickForm->isValid()) {
            $slug = $this->handleSubmittedForms($trick, $user, $trickForm, 'update');

            $this->addFlash('success', 'Figure modifiée avec succès');

            return $this->redirectToRoute('app_tricks_details', ['slug' => $slug]);
        }

        return $this->render('tricks/edit.html.twig', [
            'trick' => $trick,
            'images' => $images,
            'videos' => $videos,
            'trickForm' => $trickForm->createView(),
        ]);
    }

    #[Route('/supprimer/{slug}', name: 'delete')]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Trick $trick): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(DeleteTrickType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$trick) {
                $this->addFlash('danger', 'Aucune figure correspondante');

                return $this->redirectToRoute('app_tricks_index', ['category_slug' => 'all']);
            }

            $this->em->getConnection()->beginTransaction();
            $filesystem = new Filesystem();

            try {
                $images = $trick->getImages();

                $promoteImage = $trick->getPromoteImage();

                if ($promoteImage !== null) {
                    $this->em->remove($promoteImage);
                }

                $this->em->remove($trick);
                $this->em->flush();

                foreach ($images as $image) {
                    $pathToImage = 'assets/img/tricks/'.$image->getName();

                    if ($filesystem->exists($pathToImage)) {
                        $filesystem->remove($pathToImage);
                    }
                }

                $this->em->getConnection()->commit();
            } catch (\Exception $e) {
                $this->em->getConnection()->rollBack();
                throw $e;
            }

            $this->addFlash('success', 'Figure supprimée avec succès');
        }

        return $this->redirectToRoute('app_tricks_index', ['category_slug' => 'all']);
    }
}
