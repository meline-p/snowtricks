<?php

namespace App\Repository;

use App\Entity\Image;
use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Twig\Node\Expression\FunctionExpression;

/**
 * @extends ServiceEntityRepository<Image>
 *
 * @method Image|null find($id, $lockMode = null, $lockVersion = null)
 * @method Image|null findOneBy(array $criteria, array $orderBy = null)
 * @method Image[]    findAll()
 * @method Image[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
    }

    public function getImages(Trick $trick)
    {
        $trickId = $trick->getId();

        return $this->createQueryBuilder('i')
            ->where('i.trick = :trickId')
            ->setParameter('trickId', $trickId)
            ->orderBy('i.created_at', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getPromoteImage(Trick $trick)
    {
        $trickId = $trick->getId();

        return $this->createQueryBuilder('i')
            ->where('i.trick = :trickId')
            ->orderBy('i.promoteImage', 'DESC')
            ->addOrderBy('i.created_at', 'ASC')
            ->setParameter('trickId', $trickId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }


    //    /**
    //     * @return Image[] Returns an array of Image objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Image
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
