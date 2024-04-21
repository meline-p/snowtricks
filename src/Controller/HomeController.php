<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Repository\TrickRepository;
use App\Service\PromoteImageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    private $promoteImageService;

    public function __construct(PromoteImageService $promoteImageService)
    {
        $this->promoteImageService = $promoteImageService;
    }

    #[Route('/', name: 'app_main')]
    public function index(
        TrickRepository $trickRepository, 
        Request $request,
        Trick $trick
        ): Response
    {
        $page = $request->query->getInt('page', 1);
        $tricks = $trickRepository->findTricksPaginated($page, 'all', 3);

        $promoteImages = [];
        $allTricks = $trickRepository->findAll();
        foreach ($allTricks as $trick) {
            $promoteImage = $this->promoteImageService->getPromoteImage($trick);
            $promoteImages[$trick->getId()] = $promoteImage;
        }

        return $this->render('home/index.html.twig', [
            'tricks' => $tricks,
            'promoteImages' => $promoteImages
        ]);
    }
}
