<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Repository\CategoryRepository;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tricks', name: 'app_tricks_')]
class TricksController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoryRepository $categoryRepository, TrickRepository $trickRepository): Response
    {
        return $this->render('tricks/index.html.twig', [
            'categories' => $categoryRepository->findBy([],
                ['categoryOrder' => 'asc']),
            'tricks' => $trickRepository->findAll(),
        ]);
    }

    #[Route('/{slug}', name: 'details')]
    public function details(Trick $trick): Response
    {
        return $this->render('tricks/details.html.twig', compact('trick'));
    }
}
