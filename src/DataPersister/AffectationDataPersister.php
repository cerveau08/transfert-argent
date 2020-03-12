<?php

namespace App\DataPersister;

use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Affectation;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;



class AffectationDataPersister implements ContextAwareDataPersisterInterface
{
    
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage,UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Affectation;
    }
    public function persist($data, array $context = [])
    {
       
        $userConect=$this->tokenStorage->getToken()->getUser();
        $userPartenaire=$userConect->getPartenaire();
        
        $userRoles=$userConect->getRoles()[0];
        if($userRoles == "ROLE_PARTENAIRE" || $userRoles == "ROLE_ADMIN_PARTENAIRE")
        {
            $data->setUserQuiAffecte($userPartenaire);
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