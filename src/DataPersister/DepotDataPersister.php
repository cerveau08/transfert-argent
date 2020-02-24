<?php


namespace App\DataPersister;

use App\Entity\Depot;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DepotDataPersister implements ContextAwareDataPersisterInterface
{
    
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager,TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
        
    }
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Depot;
        // TODO: Implement supports() method.
    }
    public function persist($data, array $context = [])
    {
       
        $caissieradd=$this->tokenStorage->getToken()->getUser();
        
        ///variable role user connecté
        if($data->getMontant() >= 0)
     {
    $nouveauSolde = $data->getMontant() + $data->getCompte()->getSolde();
    //dd($nouveauSolde);
    $data->setCaissierAdd($caissieradd);               
      $data->getCompte()->setSolde($nouveauSolde);
     }else{
        throw new Exception("le Montant de Depot doit être superieur a 0");
    }                    
                $this->entityManager->persist($data);
                $this->entityManager->flush();
    }
   
    public function remove($data, array $context = [])
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}