<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controller class responsible for displaying the main page with a list of tricks.
 */
class HomeController extends AbstractController
{
    /**
     * Displays the main page with a paginated list of tricks.
     *
     * @param TrickRepository $trickRepository the repository used to fetch tricks from the database
     * @param Request         $request         the HTTP request object containing query parameters such as the page number
     *
     * @return Response a Response object that renders the main page view with the list of tricks
     */
    #[Route('/', name: 'app_main')]
    public function index(
        TrickRepository $trickRepository,
        Request $request,
    ): Response {
        $page = $request->query->getInt('page', 1);
        $tricks = $trickRepository->findTricksPaginated($page, 'tout', 6);

        return $this->render('home/index.html.twig', [
            'tricks' => $tricks,
        ]);
    }
}
