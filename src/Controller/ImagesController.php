<?php

namespace App\Controller;

use App\Entity\Image;
use App\Service\PictureService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
    public function deleteTrickImage(
        Image $image,
        Request $request,
    ): Response {
        $this->removePromoteImageIfCurrent($image);

        return $this->handleDeleteImage($image, $request, 'tricks');
    }

    #[Route('/profil/{user_username}/image/supprimer/{id}', name: 'app_profile_delete_picture')]
    public function deleteUserImage(
        Image $image,
        Request $request,
    ): Response {
        return $this->handleDeleteImage($image, $request, 'user');
    }

    private function removePromoteImageIfCurrent(Image $image): void
    {
        $trick = $image->getTrick();

        if ($image === $trick->getPromoteImage()) {
            $trick->setPromoteImage(null);
        }
    }

    private function handleDeleteImage(Image $image, Request $request, string $folder): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if ($this->pictureService->delete($image, $folder)) {
            $this->addFlash('success', 'Image supprimée avec succès');
        } else {
            $this->addFlash('danger', 'Erreur : impossible de supprimer cette image');
        }

        $route = $request->headers->get('referer');

        return $this->redirect($route);
    }
}
