<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Trick;
use App\Repository\CategoryRepository;
use App\Repository\ImageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tricks/categories', name: 'app_categories_')]
class CategoriesController extends AbstractController
{
    // #[Route('/{slug}', name: 'index')]
    // public function index(Category $category, CategoryRepository $categoryRepository, ImageRepository $imageRepository): Response
    // {
    //     $tricks = $category->getTricks();

    //     $promoteImages = [];
    //     foreach ($tricks as $trick) {
    //         $promoteImage = $this->getPromoteImage($trick, $imageRepository);
    //         $promoteImages[$trick->getId()] = $promoteImage;
    //     }

    //     return $this->render('categories/index.html.twig', [
    //         'category' => $category,
    //         'categories' => $categoryRepository->findall(),
    //         'tricks' => $tricks,
    //         'promoteImages' => $promoteImages
    //     ]);
    // }

    // public function getPromoteImage(
    //     Trick $trick,
    //     ImageRepository $imageRepository
    // ) {
    //     $images = $imageRepository->findBy(['trick' => $trick]);

    //     if (null !== $trick->getPromoteImage()) {
    //         return $trick->getPromoteImage();
    //     }

    //     if (count($images) > 0) {
    //         return $images[0];
    //     }

    //     return null;
    // }
}
