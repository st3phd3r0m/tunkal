<?php

namespace App\Repository;

use App\Entity\Comments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comments|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comments|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comments[]    findAll()
 * @method Comments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comments::class);
    }

    /**
     * Undocumented function.
     */
    public function getNumberOfPages(string $post_slug, int $limit = 5): int
    {
        $dql = 'SELECT COUNT(*) FROM comments as c'
                .' INNER JOIN posts ON c.post_id = posts.id'
                .' WHERE posts.slug = ? AND c.is_moderated = ?';
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->executeQuery($dql, [$post_slug, true], [ParameterType::STRING, ParameterType::BOOLEAN]);
        $result = $stmt->fetchOne();
        $conn->close();

        return (int) ceil($result / $limit);
    }

    /**
     * Undocumented function.
     */
    public function getPage(int $page, string $post_slug, int $limit = 5): object
    {
        $offset = $limit * ($page - 1);
        $dql = 'SELECT c.id, c.pseudo, c.content, c.sent_at FROM comments as c'
            .' INNER JOIN posts ON c.post_id = posts.id'
            .' WHERE posts.slug = ? AND c.is_moderated = ?'
            .' ORDER BY sent_at DESC'
            .' LIMIT ?, ?';
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->executeQuery($dql, [$post_slug, true, $offset, $limit], [ParameterType::STRING, ParameterType::BOOLEAN, ParameterType::INTEGER, ParameterType::INTEGER]);
        $result = $stmt->fetchAllAssociative();
        $conn->close();

        return (object) [
            'data' => $result,
            '_embedded' => (object) [
                'post' => $post_slug,
                'page' => $page,
                'limit' => $limit,
                'offset' => $page * $limit,
                'delivered_at' => (new \DateTime('now', new \DateTimeZone('Europe/Paris')))->format('d/m/Y H:i:s'),
            ],
        ];
    }

    // public function getPage2(int $page, int $post_id, ?bool $moderated = true, int $limit = 5)
    // {
    //     $offset = $limit * $page;
    //     $qb = $this->createQueryBuilder('c')
    //         ->select('c.id', 'c.pseudo', 'c.content', 'c.is_moderated', "DATE_FORMAT(c.sent_at, '%d-%m-%Y %H:%i:%s') as sent_at", 'p.id as post_id')
    //         ->innerJoin('c.post', 'p')
    //         ->andWhere('p.id = ?2')
    //         ->setParameter(2, $post_id);

    //     if(isset($moderated)){
    //         $qb->andWhere('c.is_moderated = ?1')
    //             ->setParameter(1, $moderated);
    //     }
    //     if(isset($post_id)){
    //         $qb->andWhere('p.id = ?2')
    //             ->setParameter(2, $post_id);
    //     }

    //     $qb->orderBy('c.sent_at', 'ASC')
    //         ->setFirstResult($offset)
    //         ->setMaxResults($limit);

    //     return $qb->getQuery()->getResult();
    // }

    // /**
    //  * @return Comments[] Returns an array of Comments objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Comments
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
