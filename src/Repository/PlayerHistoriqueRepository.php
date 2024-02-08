<?php

namespace App\Repository;

use App\Entity\PlayerHistorique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlayerHistorique>
 *
 * @method PlayerHistorique|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerHistorique|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerHistorique[]    findAll()
 * @method PlayerHistorique[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerHistoriqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerHistorique::class);
    }

//    /**
//     * @return PlayerHistorique[] Returns an array of PlayerHistorique objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PlayerHistorique
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
