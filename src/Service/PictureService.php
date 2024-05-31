<?php

namespace App\Service;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class PictureService
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function add(
        UploadedFile $picture,
        ?string $folder = '',
        ?int $width = 250,
        ?int $height = 250,
        ?string $fileName = null
    ) {
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
                    $picture_source = \imagecreatefromjpeg($picture);
                    break;
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

        // Crop the image
        // Retrieve dimensions
        $imageWidth = $picture_infos[0];
        $imageHeight = $picture_infos[1];

        // Check the orientation of the image
        switch ($imageWidth <=> $imageHeight) {
            // Portrait
            case -1:
                $squareSize = $imageWidth;
                $src_x = 0;
                $src_y = ($imageHeight - $squareSize) / 2;
                break;
                // Square
            case 0:
                $squareSize = $imageWidth;
                $src_x = 0;
                $src_y = 0;
                break;
                // Landscape
            case 1:
                $squareSize = $imageWidth;
                $src_x = ($imageWidth - $squareSize) / 2;
                $src_y = 0;
                break;
        }

        // Create a new blank image
        $resized_picture = imagecreatetruecolor($width, $height);
        imagecopyresampled($resized_picture, $picture_source, 0, 0, $src_x, $src_y, $width, $height, $squareSize, $squareSize);

        $path = $this->params->get('images_directory').$folder;

        // // Create the destination folder if it does not exist
        // if (!file_exists($path.'/mini/')) {
        //     mkdir($path.'/mini/', 0755, true);
        // }

        // // Store the cropped image
        // imagejpeg($resized_picture, $path.'/mini/'.$width.'x'.$height.'_'.$fichier);

        $picture->move($path.'/', $fichier);

        return $fichier;
    }

    public function delete(
        string $fichier,
        ?string $folder = '',
        ?int $width = 250,
        ?int $height = 250
    ) {
        if ('default.webp' !== $fichier) {
            $success = false;
            $path = $this->params->get('images_directory').$folder;

            // $mini = $path.'/mini/'.$width.'x'.$height.'_'.$fichier;

            // if (file_exists($mini)) {
            //     unlink($mini);
            //     $success = true;
            // }

            $original = $path.'/'.$fichier;
            if (file_exists($original)) {
                unlink($original);
                $success = true;
            }

            return $success;
        }

        return false;
    }
}
