<?php

namespace App\Controller;

use App\Entity\Profil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

    /** 
    * @Route("/api")
    */
    class ProfilController extends AbstractController
    {
        private $tokenStorage;
        public function __construct(TokenStorageInterface $tokenStorage)
        {
            $this->tokenStorage = $tokenStorage;
        }
        /** 
        *@Route("/role", name="role", methods={"GET"})
        */
        public function getRoles(Request $request, EntityManagerInterface $entityManager)
        {
            
            $repo = $this->getDoctrine()->getRepository(Profil::class);
            $roles = $repo->findAll();
            
            $data = [];
            $i= 0;
            $rolesUser = $this->tokenStorage->getToken()->getUser()->getRoles()[0];
            //dd($rolesUser);
            if($rolesUser ===  'ROLE_ADMIN_SYSTEM')
            {
                foreach($roles as $role)
                {
                    if($role->getLibelle() === 'ROLE_ADMIN' || $role->getLibelle() === 'ROLE_CAISSIER')
                    {
                        $data[$i]=$role;
                        $i++;
                    }
                    
                }
            }
            elseif($rolesUser ===  'ROLE_ADMIN')
            {
                
                foreach($roles as $role)
                {
                    if($role->getLibelle() === 'ROLE_CAISSIER')
                    {
                        $data[$i]=$role;
                        $i++;
                    }
                    
                }
            }
           elseif($rolesUser ===  'ROLE_PARTENAIRE')
            {
                
                foreach($roles as $role)
                {
                    if($role->getLibelle() === 'ROLE_ADMIN_PARTENAIRE' || $role->getLibelle() === 'ROLE_CAISSIER_PARTENAIRE')
                    {
                        $data[$i]=$role;
                        $i++;
                    }
                    
                }
            }
    
            else if($rolesUser ===  'ROLE_ADMIN_PARTENAIRE')
            {
               
                foreach($roles as $role)
                {
                    if($role->getLibelle() === 'ROLE_CAISSIER_PARTENAIRE')
                    {
                        $data[$i]=$role;
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