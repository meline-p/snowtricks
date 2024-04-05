<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controller class responsible for handling profile-related actions.
 */
class ProfileController extends AbstractController
{
    /**
     * Displays the user's profile.
     *
     * @return Response a response object containing the rendered profile view
     */
    #[Route('/profil', name: 'app_profile')]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }
}
