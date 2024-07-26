<?php

namespace App\Service;

use App\Entity\Image;
use App\Entity\User;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Manages images in the database: processing, deletion, updating profile pictures, and integrating with the service that handles associated files.
 */
class PictureService
{
    public function __construct(
        private readonly PictureFileService $fileService,
        private readonly EntityManagerInterface $em,
        private ImageRepository $imageRepository
    ) {
    }

    /**
     * Processes an image file by delegating the task to the file service.
     *
     * @param UploadedFile|null $imageFile The uploaded image file to process. Can be null.
     * @param string            $folder    the folder where the image should be saved
     * @param int               $width     The width of the image after processing. Default is 300.
     * @param int               $height    The height of the image after processing. Default is 300.
     *
     * @return Image|null returns an `Image` object if the processing is successful,
     *                    or null if the `imageFile` is null or processing fails
     */
    public function processImage(UploadedFile $imageFile = null, string $folder, int $width = 300, int $height = 300): ?Image
    {
        return $this->fileService->processImageFile($imageFile, $folder, $width, $height);
    }

    /**
     * Deletes an image both from the database and the file system.
     *
     * @param Image|null $image  The image object to delete. Can be null.
     * @param string     $folder the folder where the image file is stored
     *
     * @return bool Returns true if the image is successfully deleted from both the database
     *              and the file system, or if the image was null. Returns false if the image
     *              file could not be deleted from the file system.
     */
    public function delete(?Image $image, string $folder): bool
    {
        if (null === $image) {
            return true;
        }

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

    /**
     * Removes the old profile picture for a user.
     *
     * @param User   $user   the user whose old profile picture is to be removed
     * @param string $folder the folder where the profile picture file is stored
     *
     * @return bool Returns true if the old profile picture was successfully deleted
     *              or if there was no old profile picture. Returns false if the old
     *              profile picture file could not be deleted from the file system.
     */
    private function removeOldProfilePicture(User $user, string $folder): bool
    {
        $old_picture_profil = $user->getPictureSlug();

        if (null !== $old_picture_profil) {
            return $this->fileService->handleDeleteImg($old_picture_profil, $folder);
        }

        return true;
    }

    /**
     * Sets a new profile picture for the user.
     *
     * @param User         $user       the user whose profile picture is being updated
     * @param UploadedFile $newPicture the new profile picture file to be uploaded
     * @param string       $folder     the folder where the profile picture file will be stored
     *
     * @return bool returns true if the new profile picture was successfully set and saved,
     *              or false if the picture processing failed
     */
    public function setNewProfilePicture(User $user, UploadedFile $newPicture, string $folder): bool
    {
        $profilPicture = $newPicture;
        $profilPicture = $this->processImage($profilPicture, $folder);

        if (null === $profilPicture) {
            return false;
        }

        // delete old profile picture
        $this->removeOldProfilePicture($user, $folder);

        // set the new profil picture
        $user->setPictureSlug($profilPicture->getName());
        $this->em->persist($user);
        $this->em->flush();

        return true;
    }
}
