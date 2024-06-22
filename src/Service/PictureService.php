<?php

namespace App\Service;

use App\Entity\Image;
use App\Entity\User;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureService
{
    public function __construct(
        private readonly PictureFileService $fileService,
        private readonly EntityManagerInterface $em,
        private ImageRepository $imageRepository
    ) {
    }

    public function processImage(UploadedFile $imageFile = null, $folder, $width = 300, $height = 300)
    {
        return $this->fileService->processImage($imageFile, $folder, $width, $height);
    }

    public function delete(Image $image, string $folder)
    {
        // Delete image from database
        $this->em->remove($image);
        $this->em->flush();

        // Delete image file from folder
        $deleted = $this->fileService->delete($image, $folder);

        if (!$deleted) {
            return false;
        }

        return true;
    }


    public function updateProfilePicture(User $user, $newPicture, $folder): bool
    {
        // remove current profile picture
        $currentPictureProfile = $this->imageRepository->findOneBy(['name' => $user->getPictureSlug()]);

        $delete = $this->delete($currentPictureProfile, $folder);
        $this->setProfilePicture($user, $newPicture, $folder);

        if (!$delete) {
            return false;
        }

        return true;
    }

    private function setProfilePicture(User $user, $newPicture, $folder)
    {
        // set the new profil picture
        $profilPicture = $newPicture;
        $profilPicture = $this->processImage($profilPicture, $folder);

        $this->em->persist($user);
        $this->em->flush();
    }
}
