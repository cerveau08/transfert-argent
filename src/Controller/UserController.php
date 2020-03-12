<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

    /** 
    * @Route("/api")
    */
class UserController extends AbstractController
{
    private $tokenStorage;
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }
    /** 
    *@Route("/admin", name="admin", methods={"GET"})
    */
    public function getAdmin()
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
                if($user->getProfil()->getLibelle() === 'ROLE_ADMIN')
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
                if($user->getProfil()->getLibelle() === 'ROLE_ADMIN_PARTENAIRE' && $nineauser === $user->getPartenaire()->getNinea())
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

     /** 
    *@Route("/caissier", name="caissier", methods={"GET"})
    */
    public function getCaissier(Request $request, EntityManagerInterface $entityManager)
    {
        
        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = $repo->findAll();
        
        $data = [];
        $i= 0;
        $userconnect = $this->tokenStorage->getToken()->getUser();
        
        $rolesUser = $userconnect->getRoles()[0];
        //dd($rolesUser);
        if($rolesUser ===  'ROLE_ADMIN_SYSTEM' || $rolesUser === 'ROLE_ADMIN')
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
        elseif($rolesUser ===  'ROLE_PARTENAIRE' || $rolesUser === 'ROLE_ADMIN_PARTENAIRE')
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
