<?php


namespace App\DataPersister;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class UserDataPersister implements DataPersisterInterface
{
    
    
    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $userPasswordEncoder, TokenStorageInterface $tokenStorage)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }
    public function supports($data): bool
    {
        return $data instanceof User;
        // TODO: Implement supports() method.
    }
    public function persist($data)
    {
        //Recuperation de l'utilisateur qui s'est connecte
        $recupUser=$this->tokenStorage->getToken()->getUser()->getRoles()[0];
        //Recuperation de l'utilisateur a ajouter ou a modifier
        $recupUseradd=$data->getRoles()[0];
        if($recupUser=="ROLE_ADMIN_SYSTEM"){
            if($recupUseradd ==  "ROLE_ADMIN_SYSTEM"){
                throw new HttpException("401","Vous ne pouvez pas ajouter ou modifier un Administrateur systeme");
    
            }else{
                $data->setPassword($this->userPasswordEncoder->encodePassword($data, $data->getPassword()));
                
                $data->eraseCredentials();
                
                $this->entityManager->persist($data);
                $this->entityManager->flush();
            }
        }if($recupUser=="ROLE_ADMIN")
            if($recupUseradd ==  "ROLE_ADMIN_SYSTEM" || $recupUseradd ==  "ROLE_ADMIN" ){
                throw new HttpException("401","Vous n'avez pas le droit de faire cette operation");
            }else{
                $data->setPassword($this->userPasswordEncoder->encodePassword($data, $data->getPassword()));
                
                $data->eraseCredentials();
                
                $this->entityManager->persist($data);
                $this->entityManager->flush();
            }
    }
    public function remove($data)
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}