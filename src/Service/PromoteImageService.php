<?php

namespace App\Service;

use App\Entity\Trick;
use App\Repository\ImageRepository;

class PromoteImageService
{
    private $imageRepository;

    public function __construct(ImageRepository $imageRepository)
    {
        $this->imageRepository = $imageRepository;
    }
    
    /**
     * Retrieve the promotional image for a given trick.
     *
     * @param  mixed Trick $trick The trick entity for which to retrieve the promotional image.
     * @return Image|null The promotional image if found, or null if none is found.
     */
    public function getPromoteImage(Trick $trick)
    {
        $images = $this->imageRepository->findBy(['trick' => $trick]);

        if (null !== $trick->getPromoteImage()) {
            return $trick->getPromoteImage();
        }

        if (count($images) > 0) {
            return $images[0];
        }

        return null;
    }
}