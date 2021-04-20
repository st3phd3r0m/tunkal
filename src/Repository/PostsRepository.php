<?php

namespace App\Repository;

use App\Entity\Posts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Posts|null find($id, $lockMode = null, $lockVersion = null)
 * @method Posts|null findOneBy(array $criteria, array $orderBy = null)
 * @method Posts[]    findAll()
 * @method Posts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Posts::class);
    }

    /**
     * Undocumented function
     *
     * @param boolean $isPastConcert
     * @return object
     */
    public function getPosts(bool $isPastConcert): object
    {
        $dql = 'SELECT p.id, p.title, p.slug FROM posts as p'
            .' WHERE p.is_past_concert = ?';
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->executeQuery($dql, [$isPastConcert], [ParameterType::BOOLEAN]);
        $result = $stmt->fetchAllAssociative();
        $conn->close();

        return (object) [
            'data' => $result,
            '_embedded' => (object) [
                'delivered_at' => (new \DateTime('now', new \DateTimeZone('Europe/Paris')))->format('d/m/Y H:i:s'),
            ],
        ];
    }

    /**
     * @return Posts[] Returns an array of Posts objects
     */
    public function findPosts()
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.slug != :val1')
            ->setParameter('val1', 'accueil')
            ->andWhere('p.slug != :val2')
            ->setParameter('val2', 'news')
            ->andWhere('p.slug != :val3')
            ->setParameter('val3', 'previous-concerts')
            ->andWhere('p.slug != :val4')
            ->setParameter('val4', 'upcoming-concerts')
            ->andWhere('p.is_past_concert = 0')
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Posts[] Returns an array of Posts objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Posts
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
