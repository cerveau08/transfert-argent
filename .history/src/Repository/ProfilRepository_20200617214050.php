<?php

namespace App\Repository;

use App\Entity\Profil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Profil|null find($id, $lockMode = null, $lockVersion = null)
 * @method Profil|null findOneBy(array $criteria, array $orderBy = null)
 * @method Profil[]    findAll()
 * @method Profil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Profil::class);
    }

    public function findByLibelle($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.libelle = :val')
            ->setParameter('val', $value)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;
    }
    public function roleForAS()
    {
        return $this->createQueryBuilder('p')
        ->select('p.id','p.libelle')
            ->Where('p.libelle != :a OR p.libelle = :c')
            ->setParameter('a', 'ROLE_ADMIN')
            ->setParameter('c','ROLE_CAISSIER')
            ->getQuery()
            ->getResult()
        ;
    } 
     public function roleForA()
    {
        return $this->createQueryBuilder('p')
            ->select('p.id','p.libelle')
            ->Where('p.libelle = :c ')
            ->setParameter('c', 'ROLE_CAISSIER')
            ->getQuery()
            ->getResult()
        ;
    }
    public function roleForP()
    {
        return $this->createQueryBuilder('p')
        ->select('p.id','p.libelle')
            ->Where('p.libelle = :cp OR p.libelle  = :ap')
            ->setParameter('cp', 'ROLE_CAISSIER_PARTENAIRE')
            ->setParameter('ap', 'ROLE_ADMIN_PARTENAIRE')
            ->getQuery()
            ->getResult()
        ;
    }
    public function roleForPA()
    {
        return $this->createQueryBuilder('p')
        ->select('p.id','p.libelle')
            ->Where('p.libelle = :cp ')
            ->setParameter('cp', 'ROLE_CAISSIER_PARTENAIRE')
            ->getQuery()
            ->getResult()
        ;
    }

    
}
