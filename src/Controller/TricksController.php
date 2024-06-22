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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/tricks', name: 'app_tricks_')]
class TricksController extends AbstractController
{
    private $slugger;
    private $pictureService;
    private $imageRepository;
    private $videoRepository;
    private $userTrickRepository;
    private $commentRepository;
    private $trickRepository;

    public function __construct(
        private readonly EntityManagerInterface $em,
        SluggerInterface $slugger,
        PictureService $pictureService,
        ImageRepository $imageRepository,
        VideoRepository $videoRepository,
        UserTrickRepository $userTrickRepository,
        CommentRepository $commentRepository,
        TrickRepository $trickRepository
    ) {
        $this->slugger = $slugger;
        $this->pictureService = $pictureService;
        $this->imageRepository = $imageRepository;
        $this->videoRepository = $videoRepository;
        $this->userTrickRepository = $userTrickRepository;
        $this->commentRepository = $commentRepository;
        $this->trickRepository = $trickRepository;
    }

    #[Route('/categories/{category_slug}', name: 'index')]
    public function index(
        ?string $category_slug,
        CategoryRepository $categoryRepository,
        TrickRepository $trickRepository,
        Request $request
    ): Response {
        // Check if the category_slug parameter is present
        list($tricks, $category_slug) = $this->getTricksByCategory($category_slug, $categoryRepository, $trickRepository, $request);
        $categories = $categoryRepository->findAll();

        // Get the page number from the URL query parameters
        $page = $request->query->getInt('page', 1);

        // Retrieve paginated tricks based on the selected category
        $tricks = $trickRepository->findTricksPaginated($page, $category_slug, 6);

        $deleteForms = $this->createDeleteTrickFormView($tricks);

        return $this->render('tricks/index.html.twig', [
            'categories' => $categoryRepository->findBy([], ['categoryOrder' => 'asc']),
            'tricks' => $tricks,
            'categories' => $categories,
            'category_slug' => $category_slug,
            'deleteForms' => $deleteForms,
        ]);
    }

    #[Route('/ajouter', name: 'add')]
    public function add(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $trick = new Trick();
        $trickForm = $this->createForm(TricksFormType::class, $trick);
        $trickForm->handleRequest($request);

        if ($trickForm->isSubmitted() && $trickForm->isValid()) {
            /** @var User $user */
            $user = $this->getUser();
            $trick = $this->handleSubmittedForms($trick, $user, $trickForm, 'create');
            $slug = $trick->getSlug();
            return $this->redirectToRoute('app_tricks_details', ['slug' => $slug]);
        }
            
        return $this->render('tricks/add.html.twig', [
            'trickForm' => $trickForm->createView(),
        ]);
    }

    #[Route('/modifier/{slug}', name: 'edit')]
    public function edit(Trick $trick, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $trickForm = $this->createForm(TricksFormType::class, $trick);
        $trickForm->handleRequest($request);

        if ($trickForm->isSubmitted() && $trickForm->isValid()) {
            /** @var User $user */
            $user = $this->getUser();
            $trick = $this->handleSubmittedForms($trick, $user, $trickForm, 'update');
            $slug = $trick->getSlug();
            return $this->redirectToRoute('app_tricks_details', ['slug' => $slug]);
        }

        return $this->render('tricks/edit.html.twig', [
            'trick' => $trick,
            'trickForm' => $trickForm->createView(),
        ]);
    }

    #[Route('/supprimer/{slug}', name: 'delete')]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Trick $trick): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $form = $this->createForm(DeleteTrickType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleDeleteTrick($trick);
            $this->addFlash('success', 'Figure supprimée avec succès');
        }

        return $this->redirectToRoute('app_tricks_index', ['category_slug' => 'all']);
    }

    #[Route('/details/{slug}', name: 'details')]
    public function details(Trick $trick, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        $trickDetails = $this->getTrickDetails($trick, $page);

        $deleteForms = $this->createDeleteTrickFormView($trick);

        /** @var User $user */
        $user = $this->getUser();

        $comment = new Comment();
        $commentForm = $this->createForm(CommentsFormType::class, $comment);

        return $this->render('tricks/details.html.twig', [
            'trick' => $trick,
            'images' => $trickDetails['images'],
            'videos' => $trickDetails['videos'],
            'comments' => $trickDetails['comments'],
            'created_at' => $trickDetails['created_at'],
            'updated_at' => $trickDetails['updated_at'],
            'deleteForms' => $deleteForms,
            'commentForm' => $commentForm->createView(),
        ]);
    }

    private function getTricksByCategory(
        ?string $category_slug,
        CategoryRepository $categoryRepository,
    ): array {
        if ('all' === $category_slug) {
            $tricks = $this->trickRepository->findAll();
            $category_slug = 'all';
        } else {
            $categorySelected = $categoryRepository->findOneBy(['slug' => $category_slug]);
            $category_slug = $category_slug;
            $tricks = $this->trickRepository->findByCategory($categorySelected);
        }

        return [$tricks, $category_slug];
    }

    private function createDeleteTrickFormView($tricks)
    {
        $deleteForms = [];
        if (is_array($tricks)) {
            if (count($tricks) > 0) {
                foreach ($tricks['data'] as $trick) {
                    $deleteForms[$trick->getId()] = $this->createForm(DeleteTrickType::class)->createView();
                }
            }
        } else {
            $deleteForms[$tricks->getId()] = $this->createForm(DeleteTrickType::class)->createView();
        }

        return $deleteForms;
    }

    private function handleSubmittedForms(Trick $trick, User $user, $trickForm, $operation)
    {
        // ----- TRICK -------
        $slug = strtolower($this->slugger->slug($trick->getName()));
        $trick->initOrUpdate($trick->getName(), $slug, $trick->getDescription());
        
        // ----- IMAGES -------
        $images = $trickForm->get('images')->getData();
        $folder = 'tricks';

        foreach ($images as $image) {
            $this->processImageAndAddToTrick($trick, $image, $folder, 'Image');
        }

        $promoteImage = $trickForm->get('promoteImage')->getData();
        if ($promoteImage) {
            $this->processImageAndAddToTrick($trick, $promoteImage, $folder, 'Image à la une');
        }

        $this->em->persist($trick);

        // ----- USERTRICKS -------
        $userTrick = new UserTrick();
        $userTrick->init($operation, $user, $trick);
        $this->em->persist($userTrick);
    
        // Flush all changes at once
        $this->em->flush();

        // Return the trick regardless of image validity
        return $trick;
    }

    private function processImageAndAddToTrick($trick, $image, $folder, $imageType)
    {
        $processedImage = $this->pictureService->processImage($image, $folder);
        $originalName = $image->getClientOriginalName();

        if ($processedImage === null) {
            $this->addFlash('danger', "$imageType : $originalName : Format d'image incorrect.");
        } else {
            $trick->addImage($processedImage);
            if ($imageType === 'Image à la une') {
                $trick->setPromoteImage($processedImage);
            }
            $this->addFlash('success', "$imageType : $originalName : Image téléchargée avec succès.");
        }
    }

    private function handleDeleteTrick(Trick $trick)
    {
        $images = $trick->getImages();
        $promoteImage = $trick->getPromoteImage();

        if (null !== $promoteImage) {
            $trick->setPromoteImage(null);
        }

        foreach ($images as $image) {
            $deleted = $this->pictureService->delete($image, 'tricks');
            if (!$deleted) {
                $this->addFlash('danger', 'L\'image n\'a pas pu être supprimée.');
            }
        }

        $this->em->remove($trick);
        $this->em->flush();
    }

    private function getTrickDetails(Trick $trick, int $page): array
    {
        $images = $this->imageRepository->findBy(['trick' => $trick]);
        $videos = $this->videoRepository->findBy(['trick' => $trick]);
        $comments = $this->commentRepository->findCommentsPaginated($page, $trick->getId(), 3);

        $userTrickCreatedAt = $this->userTrickRepository->findOneBy(['trick' => $trick, 'operation' => 'create']);
        $created_at = $userTrickCreatedAt ? $userTrickCreatedAt->getDate() : null;

        $userTrickUpdatedAt = $this->userTrickRepository->findOneBy(['trick' => $trick, 'operation' => 'update'], ['date' => 'DESC']);
        $updated_at = $userTrickUpdatedAt ? $userTrickUpdatedAt->getDate() : null;

        return [
            'images' => $images,
            'videos' => $videos,
            'comments' => $comments,
            'created_at' => $created_at,
            'updated_at' => $updated_at,
        ];
    }
}
