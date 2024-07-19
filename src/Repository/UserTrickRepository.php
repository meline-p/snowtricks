<?php

namespace App\Repository;

use App\Entity\UserTrick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserTrick>
 *
 * @method UserTrick|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserTrick|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserTrick[]    findAll()
 * @method UserTrick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserTrickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserTrick::class);
    }
}
