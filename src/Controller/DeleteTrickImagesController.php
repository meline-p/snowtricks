<?php

namespace App\Controller;

use App\Entity\Image;
use App\Service\PictureService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controller class responsible for handling the deletion of an image associated with a trick.
 */
class DeleteTrickImagesController extends AbstractController
{
    private $pictureService;

    public function __construct(PictureService $pictureService)
    {
        $this->pictureService = $pictureService;
    }

    /**
     * Deletes an image associated with a trick.
     *
     * @param Image   $image   the Image entity that is to be deleted
     * @param Request $request the HTTP request object containing information about the request
     *
     * @return Response a Response object which could be a redirect to the trick's image management page or
     *                  any other appropriate response after the deletion process
     */
    #[Route('/tricks/{trick_slug}/image/supprimer/{id}', name: 'delete_image', requirements: ['trick_slug' => '[a-z0-9\-]+', 'id' => '\d+'])]
    public function deleteTrickImage(
        Image $image,
        Request $request,
    ): Response {
        $this->removePromoteImageIfCurrent($image);

        return $this->handleDeleteImage($image, $request, 'tricks');
    }

    /**
     * Removes the promoted status from an image if it is currently promoted.
     *
     * @param Image $image the Image entity to check and potentially remove from the promoted status
     *
     * @return void this method does not return any value
     */
    private function removePromoteImageIfCurrent(Image $image): void
    {
        $trick = $image->getTrick();

        if ($image === $trick->getPromoteImage()) {
            $trick->setPromoteImage(null);
        }
    }

    /**
     * Handles the deletion of an image and provides feedback to the user.
     *
     * @param Image   $image   the Image entity that is to be deleted
     * @param Request $request the HTTP request object containing the referring URL for redirection
     * @param string  $folder  the folder where the image is located and should be deleted from
     *
     * @return RedirectResponse a response that redirects the user back to the referring page after handling
     *                          the deletion process and providing feedback
     */
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
