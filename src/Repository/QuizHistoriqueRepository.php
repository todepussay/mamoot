<?php

namespace App\Repository;

use App\Entity\QuizHistorique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QuizHistorique>
 *
 * @method QuizHistorique|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuizHistorique|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuizHistorique[]    findAll()
 * @method QuizHistorique[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuizHistoriqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuizHistorique::class);
    }

    public function findById(int $id){
        return $this->createQueryBuilder("q")
            ->andWhere("q.user_id = :id")
            ->setParameter("id", $id)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return QuizHistorique[] Returns an array of QuizHistorique objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('q.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?QuizHistorique
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
