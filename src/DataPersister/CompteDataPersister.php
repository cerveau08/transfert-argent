<?php


namespace App\DataPersister;

use App\Entity\Depot;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Compte;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserDataPersister implements ContextAwareDataPersisterInterface
{
    
    private $entityManager;
    private $userPasswordEncoder;
    public function __construct(EntityManagerInterface $entityManager,TokenStorageInterface $tokenStorage,UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
        
    }
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Compte;
        // TODO: Implement supports() method.
    }
    public function persist($data, array $context = [])
    {
       
        $userCreateur=$this->tokenStorage->getToken()->getUser();
        
        ///variable role user connecté
        if($data->getDepot()->getMontant() >= 500000)
     {
         $data->setUserCreateur($userCreateur);
         $data->getPartenaire()->setUserCreateur($userCreateur);
         $data->getUser()->setPassword($this->userPasswordEncoder->encodePassword($data, $data->getUser()->getPassword()));
         $data->getUser()->eraseCredentials();
    $nouveauSolde = $data->getDepot()->getMontant() + $data->getSolde();
    //dd($nouveauSolde);
    $data->getDeopt()->setCaissierAdd($userCreateur);               
      $data->setSolde($nouveauSolde);
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