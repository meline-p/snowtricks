<?php

namespace App\Controller;

use App\Entity\Image;
use App\Service\PictureService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ImagesController extends AbstractController
{
    private $pictureService;

    public function __construct(PictureService $pictureService)
    {
        $this->pictureService = $pictureService;
    }

    #[Route('/tricks/{trick_slug}/image/supprimer/{id}', name: 'delete_image')]
    public function deleteImage(
        string $trick_slug,
        Image $image,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');
        if($this->pictureService->delete($image, 'tricks', $trick_slug)){
            $this->addFlash('success', 'image supprimée avec succès');
        } else {
            $this->addFlash('danger', 'Erreur : impossible de supprimer cette image');
        }

        $route = $request->headers->get('referer');

        return $this->redirect($route);
    }
}
