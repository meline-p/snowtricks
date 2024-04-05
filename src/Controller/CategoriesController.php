<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tricks/categories', name: 'app_categories_')]
class CategoriesController extends AbstractController
{
    #[Route('/{slug}', name: 'index')]
    public function index(Category $category, CategoryRepository $categoryRepository): Response
    {
        $tricks = $category->getTricks();

        return $this->render('categories/index.html.twig', [
            'category' => $category,
            'categories' => $categoryRepository->findall(),
            'tricks' => $tricks,
        ]);
    }
}
