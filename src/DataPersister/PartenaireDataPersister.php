<?php

namespace App\DataPersister;

use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;



class PartenaireDataPersister implements ContextAwareDataPersisterInterface
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
        return $data instanceof User;
        // TODO: Implement supports() method.
    }
    public function persist($data, array $context = [])
    {
       
                $userConect=$this->tokenStorage->getToken()->getUser();
        $userPartenaire=$userConect->getPartenaire();
        
        
                
                $data->eraseCredentials();
                
                $this->entityManager->persist($data);
                $this->entityManager->flush();
      
    }
    public function remove($data, array $context = [])
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}