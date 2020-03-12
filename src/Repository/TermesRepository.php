<?php

namespace App\Repository;

use App\Entity\Termes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Termes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Termes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Termes[]    findAll()
 * @method Termes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TermesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Termes::class);
    }

    // /**
    //  * @return Termes[] Returns an array of Termes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Termes
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
