<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Affectation;
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
        
        $repo = $this->getDoctrine()->getRepository(Affectation::class);
        $affects = $repo->findAll();

        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = $repo->findAll();

        // dd($affects);
        $data = [];
        $i= 0;
        $userconnect = $this->tokenStorage->getToken()->getUser();
        
        $rolesUser = $userconnect->getRoles()[0];

        $values = json_decode($request->getContent());
        // dd($values->debut);
        if($rolesUser ===  'ROLE_PARTENAIRE' || $rolesUser === 'ROLE_ADMIN_PARTENAIRE')
           {
        $nineauser = $userconnect->getPartenaire()->getNinea();
        foreach($users as $user)
              {
                //  dd($user);
              //  dd($user->getProfil()->getLibelle());
            if($user->getProfil()->getLibelle() === 'ROLE_CAISSIER_PARTENAIRE' && $nineauser === $user->getPartenaire()->getNinea())
                {
                  ;
                // dd($user->getAffectations());
                    foreach($user->getAffectations() as $affect) {
                       // dd($affect);
                        $startDate = $affect->getDateDebut()->format('Y-m-d');
                        $endDate = $affect->getDateFin()->format('Y-m-d');
                       // dd($startDate);
                       if(($startDate >= $values->debut && $startDate >= $values->fin) || ($endDate <= $values->debut && $endDate <= $values->fin)) {
                         $data[$i]=$user;
                        $i++;
                      }else{
                    $data = [
                        'status' => 401,
                        'message' => 'Désolé Pas de Caissier disponible !!!'
                        ];
                    
                    }
                    }
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
