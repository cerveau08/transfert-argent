<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Affectation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


  class UsersController extends AbstractController
    {
    private $tokenStorage;
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }
    public function __invoke(Affectation $dates)
    {
        
        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = $repo->findAll();

        
        
        $data = [];
        $i= 0;
        $userconnect = $this->tokenStorage->getToken()->getUser();
        
        $rolesUser = $userconnect->getRoles()[0];
      //  dd($userconnect);
        if($rolesUser ===  'ROLE_ADMIN_SYSTEM')
        {
            foreach($users as $user)
            {
                if($user->getProfil()->getLibelle() === 'ROLE_ADMIN' || $user->getProfil()->getLibelle() === 'ROLE_CAISSIER')
                {
                    $data[$i]=$user;
                    $i++;
                }
                
            }
        }
        elseif($rolesUser ===  'ROLE_PARTENAIRE')
        {
            $nineauser = $userconnect->getPartenaire()->getNinea();   
            foreach($users as $user)
            {
                if(($user->getProfil()->getLibelle() === 'ROLE_ADMIN_PARTENAIRE' || $user->getProfil()->getLibelle() === 'ROLE_CAISSIER_PARTENAIRE') && $nineauser === $user->getPartenaire()->getNinea())
                {
                    $data[$i]=$user;
                    $i++;
                }
                
            }
        }
        elseif($rolesUser ===  'ROLE_ADMIN')
        {
            foreach($users as $user)
            {
                if($user->getProfil()->getLibelle() === 'ROLE_CAISSIER')
                {
                    $data[$i]=$user;
                    $i++;
                }
                
            }
        }
        elseif($rolesUser ===  'ROLE_ADMIN_PARTENAIRE' || $rolesUser === 'R')
        {
            $nineauser = $userconnect->getPartenaire()->getNinea();
            foreach($users as $user)
                  {
                if($user->getProfil()->getLibelle() === 'ROLE_CAISSIER_PARTENAIRE' && $nineauser === $user->getPartenaire()->getNinea())
                    {
                        $data[$i]=$user;
                        $i++;
                    }
                }
        }
        else
        {
            $data = [
                'status' => 401,
                'message' => 'Désolé access non autorisé !!!'
                ];
            
        }
        
     return $this->json($data, 200);
    }

    
}
