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

    public function getPartCompteE($id)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
           ->select('t')
           ->from(Transaction::class, 't')
           ->where('t.compteEmetteur = :compteEmetteur')
           ->andWhere('t.etatPartE = \'attente\'')
           ->setParameter('compteEmetteur', $id);

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    public function updatePartCompteE($id)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
           ->update(Transaction::class, 't')
           ->set('t.etatPartE', "'verser'")
         //  ->from(Transaction::class, 't')
           ->where('t.compteEmetteur = :compteEmetteur')
           ->andWhere('t.etatPartE = \'attente\'')
           ->setParameter('compteEmetteur', $id);

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }
    public function updatePartCompteR($id)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
           ->update(Transaction::class, 't')
           ->set('t.etatPartR', "'verser'")
         //  ->from(Transaction::class, 't')
           ->where('t.compteRecepteur = :compteRecepteur')
           ->andWhere('t.etatPartR = \'attente\'')
           ->setParameter('compteRecepteur', $id);

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }


    public function getPartCompteR($id)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
           ->select('t')
           ->from(Transaction::class, 't')
           ->where('t.compteRecepteur = :compteRecepteur')
           ->andWhere('t.etatPartR = \'attente\'')
           ->setParameter('compteRecepteur', $id);

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    public function getSommePartEmetteur($id)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
           ->select('SUM(t.commissionE)')
           ->from(Transaction::class, 't')
           ->where('t.compteEmetteur = :compteEmetteur')
           ->andWhere('t.etatPartE = \'attente\'')
           ->setParameter('compteEmetteur', $id);

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }
    public function getSommePartRecepteur($id)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
           ->select('SUM(t.commisionR)')
           ->from(Transaction::class, 't')
           ->where('t.compteRecepteur = :compteRecepteur')
           ->andWhere('t.etatPartR = \'attente\'')
           ->setParameter('compteRecepteur', $id);

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

   
}