<?php

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    // /**
    //  * @return Transaction[] Returns an array of Transaction objects
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
    public function findOneBySomeField($value): ?Transaction
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findOneByCode($code)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
           ->select('t')
           ->from(Transaction::class, 't')
           ->where('t.code = :code')
           ->setParameter('code', $code);

        $query = $queryBuilder->getQuery();

        return $query->getOneOrNullResult();
    }

    public function getPartPart($id,$debut=[],$fin=[])
    {
        if(empty($debut)){
            $data1=$this->createQueryBuilder('t')
            ->select('t.id','t.dateEnvoi','t.commissionE')
            ->Where('t.compteEmetteur = :id ')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult()
        ;
        $data2=$this->createQueryBuilder('t')
            ->select('t.id','t.dateEnvoi','t.commisionR')
            ->Where('t.compteRecepteur = :id ')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult()
        ;
        return array_merge($data1,$data2);

    }else{
        $data1=$this->createQueryBuilder('t')
        ->select('t.id','t.dateEnvoi','t.commissionE')
        ->Where('t.compteEmetteur = :id ')
        ->andWhere('t.dateEnvoi >= :debut ')
        ->andWhere('t.dateEnvoi <= :fin ')
        ->setParameter('id', $id)
        ->setParameter('fin', $fin)
        ->setParameter('debut', $debut)
        ->getQuery()
        ->getResult()
    ;
    $data2=$this->createQueryBuilder('t')
        ->select('t.id','t.dateEnvoi','t.commisionR')
        ->Where('t.compteRecepteur = :id ')
        ->andWhere('t.dateEnvoi >= :debut ')
        ->andWhere('t.dateEnvoi <= :fin ')
        ->setParameter('id', $id)
        ->setParameter('fin', $fin)
        ->setParameter('debut', $debut)
        ->getQuery()
        ->getResult()
    ;
    return array_merge($data1,$data2);
    }
    }

    public function findPart($a,$debut=[],$fin=[])
    {
        if(empty($debut)){
            return $this->createQueryBuilder('t')
            ->select('t.id','t.part'.ucfirst($a),'t.dateEnvoi')
            ->getQuery()
            ->getResult()
        ;
    }else{
        return $this->createQueryBuilder('t')
        ->Where('t.dateEnvoi >= :debut ')
        ->andWhere('t.dateEnvoi <= :fin ')
        ->select('t.id','t.part'.ucfirst($a),'t.dateEnvoi')
        ->setParameter('fin', $fin)
        ->setParameter('debut', $debut)
        ->getQuery()
        ->getResult();
    }



    }
}