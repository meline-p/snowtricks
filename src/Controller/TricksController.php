<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\UserTrick;
use App\Form\CommentsFormType;
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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Controller class responsible for handling the display of trick details, as well as adding, editing, and deleting tricks.
 */
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

    /**
     * Displays a paginated list of tricks for a specific category.
     *
     * @param ?string            $category_slug      The slug of the category to filter tricks by. If null, no category filtering is applied.
     * @param CategoryRepository $categoryRepository the repository used to fetch category data
     * @param TrickRepository    $trickRepository    the repository used to fetch tricks data
     * @param Request            $request            the HTTP request object containing the pagination parameters
     *
     * @return Response a Response object that renders the view with the list of tricks, categories, and selected category slug
     */
    #[Route('/categories/{category_slug}', name: 'index', requirements: ['category_slug' => '[a-z0-9\-]+'])]
    public function index(
        ?string $category_slug,
        CategoryRepository $categoryRepository,
        TrickRepository $trickRepository,
        Request $request
    ): Response {
        // Check if the category_slug parameter is present
        list($tricks, $category_slug) = $this->getTricksByCategory($category_slug, $categoryRepository, $trickRepository, $request);
        $categories = $categoryRepository->findBy([], ['name' => 'ASC']);

        // Get the page number from the URL query parameters
        $page = $request->query->getInt('page', 1);

        // Retrieve paginated tricks based on the selected category
        $tricks = $trickRepository->findTricksPaginated($page, $category_slug, 6);

        return $this->render('tricks/index.html.twig', [
            'tricks' => $tricks,
            'categories' => $categories,
            'category_slug' => $category_slug,
        ]);
    }

    /**
     * Handles the creation of a new trick and displays the form for it.
     *
     * @param Request            $request            the HTTP request object containing form data
     * @param CategoryRepository $categoryRepository the repository used to fetch categories
     *
     * @return Response a Response object that renders the form for creating a new trick or redirects
     *                  to the trick details page upon successful form submission
     */
    #[Route('/ajouter', name: 'add')]
    public function add(Request $request, CategoryRepository $categoryRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $categories = $categoryRepository->findAll();

        $trick = new Trick();
        $trickForm = $this->createForm(TricksFormType::class, $trick);
        $trickForm->handleRequest($request);

        if ($trickForm->isSubmitted() && $trickForm->isValid()) {
            /** @var User $user */
            $user = $this->getUser();
            $trick = $this->handleSubmittedForms($trick, $user, $trickForm, 'create', $categoryRepository);
            $slug = $trick->getSlug();

            $this->addFlash('success', 'La Figure '.$trick->getName().' a bien été ajoutée');

            return $this->redirectToRoute('app_tricks_details', ['slug' => $slug]);
        }

        return $this->render('tricks/add.html.twig', [
            'trickForm' => $trickForm->createView(),
            'categories' => $categories,
        ]);
    }

    /**
     * Handles the editing of an existing trick and displays the form for it.
     *
     * @param Trick              $trick              the trick entity to be edited, retrieved by slug
     * @param Request            $request            the HTTP request object containing form data
     * @param CategoryRepository $categoryRepository the repository used to fetch categories
     *
     * @return Response a Response object that renders the form for editing the trick or redirects
     *                  to the trick details page upon successful form submission
     */
    #[Route('/modifier/{slug}', name: 'edit', requirements: ['category_slug' => '[a-z0-9\-]+'])]
    public function edit(Trick $trick, Request $request, CategoryRepository $categoryRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $categories = $categoryRepository->findAll();

        $trickForm = $this->createForm(TricksFormType::class, $trick);
        $trickForm->handleRequest($request);

        if ($trickForm->isSubmitted() && $trickForm->isValid()) {
            /** @var User $user */
            $user = $this->getUser();
            $trick = $this->handleSubmittedForms($trick, $user, $trickForm, 'update', $categoryRepository);
            $slug = $trick->getSlug();

            $this->addFlash('success', 'La Figure '.$trick->getName().' a bien été modifiée');

            return $this->redirectToRoute('app_tricks_details', ['slug' => $slug]);
        }

        return $this->render('tricks/edit.html.twig', [
            'trick' => $trick,
            'trickForm' => $trickForm->createView(),
            'categories' => $categories,
        ]);
    }

    /**
     * Deletes a specific trick and redirects the user to the list of tricks.
     *
     * @param Request $request the HTTP request object
     * @param Trick   $trick   the trick entity to be deleted, retrieved by slug
     *
     * @return Response a Response object that redirects the user to the tricks index page after
     *                  successful deletion
     */
    #[Route('/supprimer/{slug}', name: 'delete', requirements: ['category_slug' => '[a-z0-9\-]+'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Trick $trick): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $this->handleDeleteTrick($trick);
        $this->addFlash('success', 'Figure supprimée avec succès');

        return $this->redirectToRoute('app_tricks_index', ['category_slug' => 'tout']);
    }

    /**
     * Displays the details of a specific trick along with its associated images, videos, and comments.
     *
     * @param Trick   $trick   the trick entity to be displayed, identified by slug
     * @param Request $request the HTTP request object containing query parameters for pagination
     *
     * @return Response a Response object that renders the trick details page
     */
    #[Route('/details/{slug}', name: 'details', requirements: ['category_slug' => '[a-z0-9\-]+'])]
    public function details(Trick $trick, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        $trickDetails = $this->getTrickDetails($trick, $page);

        /** @var User $user */
        $user = $this->getUser();

        $comment = new Comment();
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
            'images' => $trickDetails['images'],
            'videos' => $trickDetails['videos'],
            'comments' => $trickDetails['comments'],
            'created_at' => $trickDetails['created_at'],
            'updated_at' => $trickDetails['updated_at'],
            'commentForm' => $commentForm->createView(),
        ]);
    }

    /**
     * Retrieves tricks based on the specified category slug.
     *
     * @param ?string            $category_slug      the slug of the category to filter tricks by, or 'tout' to get all tricks
     * @param CategoryRepository $categoryRepository the repository to access category data
     *
     * @return array an array where the first element is a collection of tricks and the second element is the category slug
     */
    private function getTricksByCategory(
        ?string $category_slug,
        CategoryRepository $categoryRepository,
    ): array {
        if ('tout' === $category_slug) {
            $tricks = $this->trickRepository->findAll();
            $category_slug = 'tout';
        } else {
            $categorySelected = $categoryRepository->findOneBy(['slug' => $category_slug]);
            $category_slug = $category_slug;
            $tricks = $this->trickRepository->findByCategory($categorySelected);
        }

        return [$tricks, $category_slug];
    }

    /**
     * Handles the form submission for a Trick entity, including category assignment, image processing, and user association.
     *
     * @param Trick              $trick              the Trick entity being processed
     * @param User               $user               The user associated with the operation (e.g., the creator or updater).
     * @param FormInterface      $trickForm          the form interface containing submitted data
     * @param string             $operation          the type of operation being performed ('create' or 'update')
     * @param CategoryRepository $categoryRepository the repository used to access category data
     *
     * @return Trick the updated or newly created Trick entity
     */
    private function handleSubmittedForms(Trick $trick, User $user, FormInterface $trickForm, string $operation, CategoryRepository $categoryRepository): Trick
    {
        // ----- CATEGORIES -----
        $categoryName = $trickForm->get('categoryName')->getData();
        $category = $categoryRepository->findOneBy(['name' => $categoryName]);

        if (null === $category) {
            $newCategory = new Category();
            $newCategory->setName($categoryName);
            $newCategory->setSlug(strtolower($this->slugger->slug($categoryName)));
            $category = $newCategory;

            $this->em->persist($category);
        }

        // ----- TRICK -------
        $slug = strtolower($this->slugger->slug($trick->getName()));
        $name = ucfirst($trick->getName());
        $trick->initOrUpdate($name, $slug, $trick->getDescription());
        $trick->setCategory($category);

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

    /**
     * Processes an uploaded image file and associates it with a Trick entity.
     *
     * @param Trick        $trick     the Trick entity to which the image will be added
     * @param UploadedFile $image     the uploaded image file to be processed
     * @param string       $folder    the folder where the processed image will be stored
     * @param string       $imageType The type of the image (e.g., 'Image' or 'Image à la une').
     */
    private function processImageAndAddToTrick(Trick $trick, UploadedFile $image, string $folder, string $imageType): void
    {
        $processedImage = $this->pictureService->processImage($image, $folder);
        $originalName = $image->getClientOriginalName();

        if (null === $processedImage) {
            $this->addFlash('danger', "$imageType : $originalName : Format d'image incorrect.");
        } else {
            $trick->addImage($processedImage);
            if ('Image à la une' === $imageType) {
                $trick->setPromoteImage($processedImage);
            }
            $this->addFlash('success', "$imageType : $originalName : Image téléchargée avec succès.");
        }
    }

    /**
     * Handles the deletion of a Trick entity, including its associated images.
     *
     * @param Trick $trick the Trick entity to be deleted
     */
    private function handleDeleteTrick(Trick $trick): void
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

    /**
     * Retrieves detailed information about a specific Trick entity.
     *
     * @param Trick $trick the Trick entity for which details are being retrieved
     * @param int   $page  the current page number for paginated comments
     *
     * @return array An associative array containing the following details:
     *               - 'images': Array of images associated with the Trick.
     *               - 'videos': Array of videos associated with the Trick.
     *               - 'comments': Array of paginated comments for the Trick.
     *               - 'created_at': Date the Trick was created by the user, or null if not available.
     *               - 'updated_at': Date the Trick was last updated by the user, or null if not available.
     */
    private function getTrickDetails(Trick $trick, int $page): array
    {
        $images = $this->imageRepository->findBy(['trick' => $trick]);
        $videos = $this->videoRepository->findBy(['trick' => $trick]);
        $comments = $this->commentRepository->findCommentsPaginated($page, $trick->getId(), 10);

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
