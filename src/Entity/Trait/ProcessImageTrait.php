<?php

namespace App\Entity\Trait;

use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\User;
use App\Service\PictureService;

trait ProcessImageTrait
{
    public function processImage($image, Trick $trick, PictureService $pictureService, $folder, $width = 300, $height = 300)
    {
        if (null != $image) {
            $img = new Image();

            // Generate a unique file name
            $fileName = uniqid().'_'.$trick->getId().'.'.$image->getClientOriginalExtension();

            // Call the add service with the specific file name
            $fichier = $pictureService->add($image, $folder, $width, $height, $fileName);
            $extension = pathinfo($fichier, PATHINFO_EXTENSION);

            $img->setName($fileName);
            $img->setExtension($extension);
            $trick->addImage($img);

            return $img;
        }

        return null;
    }

    public function processProfilPicture($image, User $user, PictureService $pictureService, $folder, $width = 300, $height = 300)
    {
        if (null != $image) {
            $img = new Image();

            // Generate a unique file name
            $fileName = uniqid().'_'.$user->getId().'.'.$image->getClientOriginalExtension();

            // Call the add service with the specific file name
            $fichier = $pictureService->add($image, $folder, $width, $height, $fileName);
            $extension = pathinfo($fichier, PATHINFO_EXTENSION);

            $img->setName($fileName);
            $img->setExtension($extension);
            $user->setPictureSlug($fileName);

            return $img;
        }

        return null;
    }
}
