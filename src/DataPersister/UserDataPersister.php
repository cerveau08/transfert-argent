<?php


namespace App\DataPersister;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
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
        return $data instanceof User;
        // TODO: Implement supports() method.
    }
    public function persist($data, array $context = [])
    {
       
                $userConect=$this->tokenStorage->getToken()->getUser();
        $userPartenaire=$userConect->getPartenaire();
        ///variable role user connecté
        $userRoles=$userConect->getRoles()[0];
        if($userRoles == "ROLE_PARTENAIRE" || $userRoles == "ROLE_ADMIN_PARTENAIRE")
        {
            $data->setPartenaire($userPartenaire);
        }
                $data->setPassword($this->userPasswordEncoder->encodePassword($data, $data->getPassword()));
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