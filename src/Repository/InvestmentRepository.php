<?php

namespace App\Repository;

use App\Entity\Investment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Investment>
 */
class InvestmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Investment::class);
    }

    public function save(Investment $investment): void
    {
        $this->getEntityManager()->persist($investment);
        $this->getEntityManager()->flush();
    }

    public function createFilteredQuery(string $ownerEmail): QueryBuilder
    {
        $qb = $this->createQueryBuilder('i')
            ->join('i.owner', 'u')
            ->orderBy('i.createdAt', 'DESC');

        if ($ownerEmail) {
            $qb->andWhere('u.email = :email')
                ->setParameter('email', $ownerEmail);
        }

        return $qb;
    }

    //    /**
    //     * @return Investment[] Returns an array of Investment objects
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

    //    public function findOneBySomeField($value): ?Investment
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
