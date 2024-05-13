<?php

namespace App\Controller;

use App\Entity\Image;
use App\Repository\TrickRepository;
use App\Service\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ImagesController extends AbstractController
{
    #[Route('/tricks/{trick_slug}/image/supprimer/{id}', name: 'delete_image')]
    public function deleteImage(
        string $trick_slug,
        Image $image,
        Request $request,
        EntityManagerInterface $em,
        PictureService $pictureService,
        TrickRepository $trickRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Retrieve image name
        $name = $image->getName();

        $trick = $trickRepository->findOneBy(['slug' => $trick_slug]);

        if ($image == $trick->getPromoteImage()) {
            $trick->setPromoteImage(null);
        }

        if ($pictureService->delete($name, 'tricks', 300, 300)) {
            // Delete image from database
            $em->remove($image);
            $em->flush();
        }

        $route = $request->headers->get('referer');

        return $this->redirect($route);
    }
}
