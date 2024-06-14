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
        $this->em->getConnection()->beginTransaction();

        try {
            // Delete image from database

            $this->em->remove($image);
            $this->em->flush();

            $deleted = $this->fileService->delete($image, $folder);

            if ($deleted) {
                $this->em->getConnection()->commit();

                return true;
            }

            throw new \Exception('not deleted');
        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack();

            return false;
        }
    }

    public function deleteImageWithPromotionCheck(Image $image, bool $promoteImage, string $folder): bool
    {
        if ($promoteImage) {
            $trick = $image->getTrick();
            $trick->setPromoteImage(null);
        }

        return $this->delete($image, $folder);
    }

    public function updateProfilePicture(User $user, $newPicture, $folder): bool
    {
        try {
            // remove current profile picture
            $currentPictureProfile = $this->imageRepository->findOneBy(['name' => $user->getPictureSlug()]);

            $this->delete($currentPictureProfile, $folder);
            $this->setProfilePicture($user, $newPicture, $folder);

            $this->em->getConnection()->commit();

            return true;
        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack();

            return false;
        }
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
