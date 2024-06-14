<?php

namespace App\Service;

use App\Entity\Trick;
use App\Entity\User;
use App\Entity\UserTrick;
use App\Repository\CommentRepository;
use App\Repository\ImageRepository;
use App\Repository\UserTrickRepository;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class TrickService
{
    private $slugger;
    private $em;
    private $pictureService;
    private $imageRepository;
    private $videoRepository;
    private $userTrickRepository;
    private $commentRepository;

    public function __construct(
        SluggerInterface $slugger,
        EntityManagerInterface $em,
        PictureService $pictureService,
        ImageRepository $imageRepository,
        VideoRepository $videoRepository,
        UserTrickRepository $userTrickRepository,
        CommentRepository $commentRepository
    ) {
        $this->slugger = $slugger;
        $this->em = $em;
        $this->pictureService = $pictureService;
        $this->imageRepository = $imageRepository;
        $this->videoRepository = $videoRepository;
        $this->userTrickRepository = $userTrickRepository;
        $this->commentRepository = $commentRepository;
    }

    public function handleSubmittedForms(Trick $trick, User $user, $trickForm, $operation)
    {
        $this->em->getConnection()->beginTransaction();

        try {
            // ----- TRICK -------
            $slug = strtolower($this->slugger->slug($trick->getName()));
            $trick->setSlug($slug);

            $description = $trick->getDescription();
            $trick->setDescription($description);

            // ----- IMAGES -------
            $images = $trickForm->get('images')->getData();
            $folder = 'tricks';
            foreach ($images as $image) {
                $img = $this->pictureService->processImage($image, $folder);
                $trick->addImage($img);
            }

            // ----- PROMOTE IMAGE -------
            $promoteImage = $trickForm->get('promoteImage')->getData();

            if ($promoteImage) {
                $promoteImg = $this->pictureService->processImage($promoteImage, $folder);
                $trick->addImage($promoteImg);
                $trick->setPromoteImage($promoteImg);
            }

            // Persist the trick with all its changes (including images)
            $this->em->persist($trick);

            // ----- USERTRICKS -------
            $userTrick = new UserTrick();
            $userTrick->setOperation($operation);
            $userTrick->setDate(new \DateTime());
            $userTrick->setUser($user);
            $userTrick->setTrick($trick);

            $this->em->persist($userTrick);

            // Flush all changes at once
            $this->em->flush();

            $this->em->getConnection()->commit();

            return $slug;
        } catch (\Exception $e) {
            // Rollback the transaction
            $this->em->getConnection()->rollBack();
        }
    }

    public function deleteTrick(Trick $trick)
    {
        $this->em->getConnection()->beginTransaction();

        try {
            $images = $trick->getImages();
            $promoteImage = $trick->getPromoteImage();

            if (null !== $promoteImage) {
                $trick->setPromoteImage(null);
            }

            foreach ($images as $image) {
                $this->pictureService->delete($image, 'tricks');
            }

            $this->em->remove($trick);
            $this->em->flush();

            $this->em->getConnection()->commit();
        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack();
            throw $e;
        }
    }

    public function getTrickDetails(Trick $trick, int $page): array
    {
        $images = $this->imageRepository->findBy(['trick' => $trick]);
        $videos = $this->videoRepository->findBy(['trick' => $trick]);
        $comments = $this->commentRepository->findCommentsPaginated($page, $trick->getId(), 3);

        $userTrickCreatedAt = $this->userTrickRepository->findOneBy(['trick' => $trick, 'operation' => 'create']);
        $created_at = $userTrickCreatedAt ? $userTrickCreatedAt->getDate() : null;

        $userTrickUpdatedAt = $this->userTrickRepository->findOneBy(['trick' => $trick, 'operation' => 'update'], ['date' => 'DESC']);
        $updated_at = $userTrickUpdatedAt ? $userTrickUpdatedAt->getDate() : null;

        return [
            'images' => $images,
            'videos' => $videos,
            'comments' => $comments,
            'created_at' => $created_at,
            'updated_at' => $updated_at,
        ];
    }
}
