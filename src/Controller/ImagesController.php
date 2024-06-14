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

    private function handleDeleteImage(Image $image, Request $request, bool $promoteImage, string $folder)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if ($promoteImage) {
            $trick = $image->getTrick();
            $trick->setPromoteImage(null);
        }

        if ($this->pictureService->deleteImageWithPromotionCheck($image, $promoteImage, $folder)) {
            $this->addFlash('success', 'Image supprimée avec succès');
        } else {
            $this->addFlash('danger', 'Erreur : impossible de supprimer cette image');
        }

        $route = $request->headers->get('referer');

        return $this->redirect($route);
    }

    #[Route('/tricks/{trick_slug}/promote_image/supprimer/{id}', name: 'delete_promote_image')]
    public function deletePromoteImage(
        Image $image,
        Request $request,
    ): Response {
        return $this->handleDeleteImage($image, $request, true, 'tricks');
    }

    #[Route('/tricks/{trick_slug}/image/supprimer/{id}', name: 'delete_image')]
    public function deleteTrickImage(
        Image $image,
        Request $request,
    ): Response {
        return $this->handleDeleteImage($image, $request, false, 'tricks');
    }

    #[Route('/profile/{user_username}/image/supprimer/{id}', name: 'app_profile_delete_picture')]
    public function deleteUserImage(
        Image $image,
        Request $request,
    ): Response {
        return $this->handleDeleteImage($image, $request, false, 'user');
    }
}
