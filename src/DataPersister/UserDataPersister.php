<?php


namespace App\DataPersister;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



class UserDataPersister implements ContextAwareDataPersisterInterface
{
    
    private $entityManager;
    private $userPasswordEncoder;
    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->entityManager = $entityManager;
    }
    public function supports($data, array $context = []): bool
    {
        return $data instanceof User;
        // TODO: Implement supports() method.
    }
    public function persist($data, array $context = [])
    {
        //Recuperation de l'utilisateur qui s'est connecte
        /*
        $recupUser=$this->tokenStorage->getToken()->getUser()->getRoles()[0];
        Recuperation de l'utilisateur a ajouter ou a modifier
        $recupUseradd=$data->getRoles()[0];
        if($recupUser=="ROLE_ADMIN_SYSTEM"){
           if($recupUseradd ==  "ROLE_ADMIN_SYSTEM"){
               throw new HttpException("401","Vous ne pouvez pas ajouter ou modifier un Administrateur systeme");
    
            }else{ 
                */
                $data->setPassword($this->userPasswordEncoder->encodePassword($data, $data->getPassword()));
               // $data->setImage($data->getImage());

                $data->eraseCredentials();
                
                $this->entityManager->persist($data);
                $this->entityManager->flush();
          //  }
      /*  }if($recupUser=="ROLE_ADMIN")
            if($recupUseradd ==  "ROLE_ADMIN_SYSTEM" || $recupUseradd ==  "ROLE_ADMIN" ){
                throw new HttpException("401","Vous n'avez pas le droit de faire cette operation");
            }else{
                $data->setPassword($this->userPasswordEncoder->encodePassword($data, $data->getPassword()));
                $data->setImage($data->getImage());

                $data->eraseCredentials();
                
                $this->entityManager->persist($data);
                $this->entityManager->flush();
            }*/
    }
    public function remove($data, array $context = [])
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}