<?php

namespace App\Controller;

use App\Form\DeleteTrickType;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(
        TrickRepository $trickRepository,
        Request $request,
    ): Response {
        $page = $request->query->getInt('page', 1);
        $tricks = $trickRepository->findTricksPaginated($page, 'all', 3);

        $deleteForms = [];
        if (count($tricks) > 0) {
            foreach ($tricks['data'] as $trick) {
                $deleteForms[$trick->getId()] = $this->createForm(DeleteTrickType::class)->createView();
            }
        }

        return $this->render('home/index.html.twig', [
            'tricks' => $tricks,
            'deleteForms' => $deleteForms,
        ]);
    }
}
