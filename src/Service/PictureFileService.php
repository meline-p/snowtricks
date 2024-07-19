<?php

namespace App\Service;

use App\Entity\Image;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureFileService
{
    private string $imagesDirectory;
    private Filesystem $filesystem;

    public function __construct(ParameterBagInterface $params, Filesystem $filesystem)
    {
        $this->imagesDirectory = $params->get('images_directory');
        $this->filesystem = $filesystem;
    }

    public function processImage(UploadedFile $imageFile = null, string $folder, int $width = 300, int $height = 300): ?Image
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

    public function delete(Image $image, string $folder): bool
    {
        $file_name = $image->getName();

        if ('img.png' == $file_name) {
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
