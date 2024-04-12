<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\UserTrick;
use App\Repository\CategoryRepository;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use App\Repository\UserTrickRepository;
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
    public function details(
        Trick $trick, 
        UserTrickRepository $userTrickRepository,
        ImageRepository $imageRepository
        ): Response
    {
        $trick_created_at = $userTrickRepository->getCreatedAtDate($trick);
        $trick_updated_at = $userTrickRepository->getUpdatedAtDate($trick);
        $trick_images = $imageRepository->getImages($trick);
        $trick_promote_image = $imageRepository->getPromoteImage($trick);

        // $image_name = $trick_images->getId().'_'.$trick->getId().'.'.$trick_images->getExtension();
        
        return $this->render('tricks/details.html.twig', [
            'trick' => $trick,
            'created_at' => $trick_created_at['date'],
            'updated_at' => $trick_updated_at['date'],
            'images' => $trick_images,
            'promote_image' => $trick_promote_image
        ]);
    }
}
