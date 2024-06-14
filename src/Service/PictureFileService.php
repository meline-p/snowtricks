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

    public function processImage(UploadedFile $imageFile = null, $folder, $width = 300, $height = 300): ?Image
    {
        if (null === $imageFile) {
            return null;
        }

        $img = new Image();

        $fileName = uniqid().'.'.$imageFile->getClientOriginalExtension();
        $fichier = $this->add($imageFile, $folder, $fileName, $width, $height);

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
            throw new \Exception('Format d\'image incorrect');
        }

        // Check the image format
        try {
            switch ($picture_infos['mime']) {
                case 'image/png':
                    $picture_source = \imagecreatefrompng($picture);
                    break;
                case 'image/jpg':
                case 'image/jpeg':
                    $picture_source = \imagecreatefromjpeg($picture);
                    break;
                case 'image/webp':
                    $picture_source = \imagecreatefromwebp($picture);
                    break;
                case 'image/avif':
                    $picture_source = \imagecreatefromavif($picture);
                    break;
                default:
                    throw new \Exception('Format d\'image incorrect');
            }
        } catch (\Exception) {
            throw new \Exception('Format d\'image incorrect');
        }

        $path = $this->imagesDirectory.$folder;

        $picture->move($path, $fichier);

        return $fichier;
    }

    public function delete(Image $image, $folder): bool
    {
        $file_name = $image->getName();

        if ('img.png' == $file_name) {
            return true;
        }

        try {
            $filesystem = new Filesystem();

            $pathToImage = $this->imagesDirectory.$folder.'/'.$file_name;

            if ($this->filesystem->exists($pathToImage)) {
                $this->filesystem->remove($pathToImage);

                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
