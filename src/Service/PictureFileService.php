<?php

namespace App\Service;

use App\Entity\Image;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Manages image files: processing, adding, MIME type handling, and deletion.
 */
class PictureFileService
{
    private string $imagesDirectory;
    private Filesystem $filesystem;

    public function __construct(ParameterBagInterface $params, Filesystem $filesystem)
    {
        $this->imagesDirectory = $params->get('images_directory');
        $this->filesystem = $filesystem;
    }

    /**
     * Processes an uploaded image file by creating an Image entity.
     *
     * @param UploadedFile|null $imageFile The image file to be processed. If null, returns null.
     * @param string            $folder    the folder where the image file will be saved
     * @param int               $width     The width to which the image should be resized. Default is 300.
     * @param int               $height    The height to which the image should be resized. Default is 300.
     *
     * @return Image|null returns an Image entity with the file name and extension, or null if the file is invalid
     */
    public function processImageFile(UploadedFile $imageFile = null, string $folder, int $width = 300, int $height = 300): ?Image
    {
        if (null === $imageFile) {
            return null;
        }

        $img = new Image();

        $fileName = uniqid().'.'.$imageFile->getClientOriginalExtension();
        $fichier = $this->add($imageFile, $folder, $fileName, $width, $height);

        if ('invalid' === $fichier) {
            return null;
        }

        $extension = pathinfo($fichier, PATHINFO_EXTENSION);

        $img->setName($fileName);
        $img->setExtension($extension);

        return $img;
    }

    /**
     * Moves and processes an uploaded image file to a specified folder with optional resizing.
     *
     * @param UploadedFile $picture  the uploaded image file to be processed
     * @param string       $folder   the folder where the image file will be saved
     * @param string       $fileName the new name of the image file
     * @param int|null     $width    The width to which the image should be resized (optional). Default is 300.
     * @param int|null     $height   The height to which the image should be resized (optional). Default is 300.
     *
     * @return string returns the file name if successful, or 'invalid' if the file is not valid
     */
    private function add(
        UploadedFile $picture,
        string $folder,
        string $fileName,
        ?int $width = 300,
        ?int $height = 300,
    ): string {
        // Assign a new name to the image file
        $fichier = $fileName;

        // Retrieve image information
        $picture_infos = getimagesize($picture);

        if (false === $picture_infos) {
            return 'invalid';
        }

        // Check the image format
        if (!$this->checkMimeType($picture, $picture_infos['mime'])) {
            return 'invalid';
        }

        $path = $this->imagesDirectory.$folder;

        $picture->move($path, $fichier);

        return $fichier;
    }

    /**
     * Checks if the provided image file's MIME type is valid and supported.
     *
     * @param UploadedFile $picture  the uploaded image file to be checked
     * @param string       $mimeType the MIME type of the image file to be validated
     *
     * @return bool returns true if the MIME type is valid and the image can be processed, otherwise false
     */
    private function checkMimeType(UploadedFile $picture, string $mimeType): bool
    {
        try {
            switch ($mimeType) {
                case 'image/png':
                    $picture_source = \imagecreatefrompng($picture);

                    return true;
                case 'image/jpg':
                case 'image/jpeg':
                    $picture_source = \imagecreatefromjpeg($picture);

                    return true;
                case 'image/webp':
                    $picture_source = \imagecreatefromwebp($picture);

                    return true;
                case 'image/avif':
                    $picture_source = \imagecreatefromavif($picture);

                    return true;
                default:
                    return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Deletes an image file from the specified folder.
     *
     * @param Image  $image  the image object containing the file name to be deleted
     * @param string $folder the folder where the image file is stored
     *
     * @return bool returns true if the file was successfully deleted, otherwise false
     */
    public function delete(Image $image, string $folder): bool
    {
        $file_name = $image->getName();

        return $this->handleDeleteImg($file_name, $folder);
    }

    /**
     * Handles the deletion of an image file from the specified folder.
     *
     * @param string $file_name the name of the file to be deleted
     * @param string $folder    the folder where the file is located
     *
     * @return bool Returns true if the file was successfully deleted or if the file
     *              name was 'img.png', otherwise returns false.
     */
    public function handleDeleteImg(string $file_name, string $folder): bool
    {
        if ('img.png' === $file_name) {
            return true;
        }

        $pathToImage = $this->imagesDirectory.$folder.'/'.$file_name;

        if ($this->filesystem->exists($pathToImage)) {
            $this->filesystem->remove($pathToImage);

            return true;
        }

        return false;
    }
}
