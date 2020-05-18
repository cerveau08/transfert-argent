<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Affectation;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AffectationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/api")
 */
class AffectController extends AbstractController
{
    
        private $tokenStorage;
        public function __construct(TokenStorageInterface $tokenStorage, AffectationRepository $affectCompteRepo)
        {
            $this->tokenStorage = $tokenStorage;
            $this->affectCompteRepo = $affectCompteRepo;
        }
        /**
         * @Route("/affect", name="affect", methods={"GET"})
         */
        public function getUsersToAffect(Request $request)
        {   
            $repo = $this->getDoctrine()->getRepository(Affectation::class);
            $affects = $repo->findAll();

            $repo = $this->getDoctrine()->getRepository(User::class);
            $users = $repo->findAll();
          // dd($users);
            $data = [];
            $j= 0;
            $userconnect = $this->tokenStorage->getToken()->getUser();
            
            $rolesUser = $userconnect->getRoles()[0];

            $values = json_decode($request->getContent());
           // dd($values);
            if($rolesUser ===  'ROLE_PARTENAIRE' || $rolesUser === 'ROLE_ADMIN_PARTENAIRE')
               {
            $nineauser = $userconnect->getPartenaire()->getNinea();
            foreach($users as $user)
                  {
                if($user->getProfil()->getLibelle() === 'ROLE_CAISSIER_PARTENAIRE' && $nineauser === $user->getPartenaire()->getNinea())
                    {
                    //  dd($affects);
                    $affectaCompte = $user->getAffectations()[0];
                    //dd($affecta);
                   // $affectCompte = $this->affectCompteRepo->findAll();
                   // dd($affectCompte);    
                    // dd($affectCompte);
                    for ($i=0; $i <count($affectCompte) ; $i++) { 
                        $dateDebut = $affectCompte[$i]->getDateDebut();
                        // dd($dateDebut);
                        $dateFin = $affectCompte[$i]->getDateFin();
                        $userAffect = $affectCompte[$i]->getUserComptePartenaire();
                        
                        if(($dateDebut >= $values->debut && $dateDebut >= $values->fin) || ($dateFin <= $values->debut && $dateFin <= $values->fin)) {
                          $data[$j]=$user;
                          $j++;
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
