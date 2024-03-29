<?php

namespace App\Repository;

use App\Entity\Quiz;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Quiz>
 *
 * @method Quiz|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quiz|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quiz[]    findAll()
 * @method Quiz[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuizRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quiz::class);
    }

    public function findByUser(int $id){
        return $this->createQueryBuilder("q")
            ->andWhere("q.user = :val")
            ->setParameter("val", $id)
            ->getQuery()
            ->getResult();
    }

    public function findById(int $id){
        return $this->createQueryBuilder("q")
            ->leftJoin('q.questions', 'question')
            ->addSelect('question')
            ->leftJoin("question.reponses", "reponse")
            ->addSelect("reponse")
            ->andWhere("q.id = :id")
            ->setParameter("id", $id)
            ->getQuery()
            ->getResult();
    }

    public function findAllQuiz(){
        return $this->createQueryBuilder("q")
            #questions du quiz
            ->leftJoin('q.questions', 'question')
            ->addSelect('question')

            #reponses des questions
            ->leftJoin("question.reponses", "reponse")
            ->addSelect("reponse")
            #utilisateurs
            ->leftJoin("q.user", "user")
            ->addSelect("user")
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Quiz[] Returns an array of Quiz objects
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

//    public function findOneBySomeField($value): ?Quiz
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
