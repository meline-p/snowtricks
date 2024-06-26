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
use Symfony\Component\Filesystem\Filesystem;


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

            $em->getConnection()->beginTransaction();

            try{
                // Delete image from database
                $em->remove($image);
                $em->flush();

                // Delete image from folder
                $filesystem = new Filesystem();
                $pathToImage = 'assets/img/tricks/' . $image->getName();

                if ($filesystem->exists($pathToImage)) {
                    $filesystem->remove($pathToImage);
                } 

                $em->getConnection()->commit();

            }catch (\Exception $e) {
                $em->getConnection()->rollBack();
                throw $e;
            }

            $this->addFlash('success', 'image supprimée avec succès');
        } else {
            $this->addFlash('danger', 'Erreur : impossible de supprimer cette image');
        }

        $route = $request->headers->get('referer');

        return $this->redirect($route);
    }
}
