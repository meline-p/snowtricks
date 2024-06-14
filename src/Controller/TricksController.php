<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use App\Form\CommentsFormType;
use App\Form\DeleteTrickType;
use App\Form\TricksFormType;
use App\Repository\CategoryRepository;
use App\Repository\TrickRepository;
use App\Service\TrickService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/tricks', name: 'app_tricks_')]
class TricksController extends AbstractController
{
    private TrickService $trickService;

    public function __construct(
        TrickService $trickService,
        private readonly EntityManagerInterface $em
    ) {
        $this->trickService = $trickService;
    }

    private function getTricksByCategory(
        ?string $category_slug,
        CategoryRepository $categoryRepository,
        TrickRepository $trickRepository
    ): array {
        if ('all' === $category_slug) {
            $tricks = $trickRepository->findAll();
            $category_slug = 'all';
        } else {
            $categorySelected = $categoryRepository->findOneBy(['slug' => $category_slug]);
            $category_slug = $category_slug;
            $tricks = $trickRepository->findByCategory($categorySelected);
        }

        return [$tricks, $category_slug];
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

        $deleteForms = [];
        if (count($tricks) > 0) {
            foreach ($tricks['data'] as $trick) {
                $deleteForms[$trick->getId()] = $this->createForm(DeleteTrickType::class)->createView();
            }
        }

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
            $slug = $this->trickService->handleSubmittedForms($trick, $user, $trickForm, 'create');
            $this->addFlash('success', 'Figure ajoutée avec succès');

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
            $slug = $this->trickService->handleSubmittedForms($trick, $user, $trickForm, 'update');
            $this->addFlash('success', 'Figure modifiée avec succès');

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
            $this->trickService->deleteTrick($trick);
            $this->addFlash('success', 'Figure supprimée avec succès');
        }

        return $this->redirectToRoute('app_tricks_index', ['category_slug' => 'all']);
    }

    #[Route('/details/{slug}', name: 'details')]
    public function details(Trick $trick, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        $trickDetails = $this->trickService->getTrickDetails($trick, $page);

        $deleteForms[$trick->getId()] = $this->createForm(DeleteTrickType::class)->createView();

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
}
