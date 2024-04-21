<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Repository\CategoryRepository;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use App\Service\PromoteImageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tricks', name: 'app_tricks_')]
class TricksController extends AbstractController
{
    private $promoteImageService;

    public function __construct(PromoteImageService $promoteImageService)
    {
        $this->promoteImageService = $promoteImageService;
    }

    #[Route('/{category_slug}', name: 'index')]
    public function index(
        ?string $category_slug,
        CategoryRepository $categoryRepository,
        TrickRepository $trickRepository,
        Request $request
    ): Response {
        // Check if the category_slug parameter is present
        if ($category_slug === "all") {
            $tricks = $trickRepository->findAll();
            $category_slug = 'all';
        } else {
            $categorySelected = $categoryRepository->findOneBy(['slug' => $category_slug]);
            $category_slug = $category_slug;
            $tricks = $trickRepository->findByCategory($categorySelected);
        }

        $categories = $categoryRepository->findAll();

        // Get the page number from the URL query parameters
        $page = $request->query->getInt('page', 1);

        // Retrieve paginated tricks based on the selected category
        $tricks = $trickRepository->findTricksPaginated($page, $category_slug, 3);

        $promoteImages = [];
        $allTricks = $trickRepository->findAll();
        foreach ($allTricks as $trick) {
            $promoteImage = $this->promoteImageService->getPromoteImage($trick);
            $promoteImages[$trick->getId()] = $promoteImage;
        }

        return $this->render('tricks/index.html.twig', [
            'categories' => $categoryRepository->findBy([], ['categoryOrder' => 'asc']),
            'tricks' => $tricks,
            'promoteImages' => $promoteImages,
            'categories' => $categories,
            'category_slug' => $category_slug
        ]);
    }


    #[Route('/details/{slug}', name: 'details')]
    public function details(
        Trick $trick,
        ImageRepository $imageRepository,
    ): Response {
        $images = $imageRepository->findBy(['trick' => $trick]);

        $promoteImage = $this->promoteImageService->getPromoteImage($trick);

        return $this->render('tricks/details.html.twig', [
            'trick' => $trick,
            'images' => $images,
            'promoteImage' => $promoteImage,
        ]);
    }
}
