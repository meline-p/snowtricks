<?php

namespace App\Repository;

use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trick>
 *
 * @method Trick|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trick|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trick[]    findAll()
 * @method Trick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trick::class);
    }

    public function findTricksPaginated(int $page, string $slug, int $limit = 3): array
    {
        $limit = abs($limit);

        $result = [];

        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('c', 't', 'ut')
            ->from('App\Entity\Trick', 't')
            ->innerJoin('t.category', 'c')
            ->innerJoin('t.userTricks', 'ut')
            ->andWhere('ut.operation = :operation')
            ->setParameter('operation', 'create')
            ->orderBy('ut.date', 'DESC');

        // If the category slug is not 'all', filter by category
        if ('tout' !== $slug) {
            $queryBuilder->andWhere('c.slug = :slug')
                ->setParameter('slug', $slug);
        }

        $queryBuilder->setMaxResults($limit)
            ->setFirstResult(($page * $limit) - $limit);

        $paginator = new Paginator($queryBuilder);
        $data = $paginator->getQuery()->getResult();

        // Check if there is no data
        if (empty($data)) {
            return $result;
        }

        // Calculate the number of pages
        $pages = ceil($paginator->count() / $limit);

        // Populate the result array with data
        $result['data'] = $data;
        $result['pages'] = $pages;
        $result['page'] = $page;
        $result['limit'] = $limit;

        return $result;
    }
}
