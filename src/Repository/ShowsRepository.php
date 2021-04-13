<?php

namespace App\Repository;

use App\Entity\Shows;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Shows|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shows|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shows[]    findAll()
 * @method Shows[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShowsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Shows::class);
    }

    /**
     * @return Shows[] Returns an array of Shows objects
     */
    public function findExpectedConcerts()
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.expected_at > CURRENT_TIMESTAMP()')
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Shows[] Returns an array of Shows objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Shows
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
