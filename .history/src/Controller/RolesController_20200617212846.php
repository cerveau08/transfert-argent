<?php

namespace App\Controller;

use App\Repository\ProfilRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;



class RolesController extends AbstractController
{
   public function __construct(ProfilRepository $role,TokenStorageInterface $tokenStorage){
    $this->role=$role;
    $this->tokenStorage=$tokenStorage;
   }

    public function __invoke()
    {
     
      $userRole=$this->tokenStorage->getToken()->getUser()->getRoles()[0];
     
      if($userRole==="ROLE_ADMIN_SYSTEM")
      {
          return $this->role->roleForAS();
      }
      elseif($userRole==="ROLE_ADMIN")
      {
          return $this->role->roleForA();
      }elseif($userRole==="ROLE_PARTENAIRE")
      {
          return $this->role->roleForP();
      }elseif($userRole==="ROLE_ADMIN_PARTENAIRE")
      {
          return $this->role->roleForPA();
      }



    }
}