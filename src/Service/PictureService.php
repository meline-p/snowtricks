<?php

namespace App\Service;

use App\Entity\Image;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PictureService
{
    private $params;
    private $em;
    private $trickRepository;

    public function __construct(ParameterBagInterface $params, EntityManagerInterface $em, TrickRepository $trickRepository)
    {
        $this->params = $params;
        $this->em = $em;
        $this->trickRepository = $trickRepository;
    }

    public function processImage(UploadedFile $imageFile = null, $folder, $width = 300, $height = 300)
    {
        if (null === $imageFile) {
            return null;
        }

        $img = new Image();

        // Generate a unique file name
        $fileName = uniqid().'.'.$imageFile->getClientOriginalExtension();

        // Call the add service with the specific file name
        $fichier = $this->add($imageFile, $folder, $fileName, $width, $height);
        $extension = pathinfo($fichier, PATHINFO_EXTENSION);

        $img->setName($fileName);
        $img->setExtension($extension);

        return $img;
    }

    private function add(
        UploadedFile $picture,
        ?string $folder = '',
        string $fileName = null,
        ?int $width = 300,
        ?int $height = 300,
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
        
        $path = $this->params->get('images_directory').$folder;

        $picture->move($path, $fichier);

        return $fichier;
    }

    public function delete(
        Image $image,
        $folder,
        string $trick_slug = null,
    ) {
        // $this->denyAccessUnlessGranted('ROLE_USER');

        // Retrieve image name
        $file_name = $image->getName();

        if ('tricks' === $folder && null !== $trick_slug) {
            $trick = $this->trickRepository->findOneBy(['slug' => $trick_slug]);

            if ($image == $trick->getPromoteImage()) {
                $trick->setPromoteImage(null);
            }
        }

        $this->em->getConnection()->beginTransaction();

        try {
            // Delete image from database
            $this->em->remove($image);
            $this->em->flush();

            // Delete image from folder
            $filesystem = new Filesystem();

            $pathToImage = $this->params->get('images_directory').$folder.'/'.$file_name;

            if ($filesystem->exists($pathToImage)) {
                $filesystem->remove($pathToImage);
            }

            $this->em->getConnection()->commit();

            return true;
        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack();
            throw $e;

            return false;
        }
    }
}
