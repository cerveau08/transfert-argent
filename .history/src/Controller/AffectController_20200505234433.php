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
class AffectController extends AbstractController
{
    
        private $tokenStorage;
        public function __construct(TokenStorageInterface $tokenStorage, AffectationRepository $affectCompteRepo)
        {
            $this->tokenStorage = $tokenStorage;
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
            $i= 0;
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
                    $affectCompte = $this->affectCompteRepo->findAll();
                        
                    // dd($affectCompte);
                    for ($i=0; $i <count($affectCompte) ; $i++) { 
                        $dateDebut = $affectCompte[$i]->getDateDebut();
                        // dd($dateDebut);
                        $dateFin = $affectCompte[$i]->getDateFin();
                        $userAffect = $affectCompte[$i]->getUserComptePartenaire();
                        
                        if ($userAffect == $data->getUserComptePartenaire() && ( ($dateDebut <= $data->getDateDebut() && $dateFin >= $data->getDateDebut()) || ($dateDebut <= $data->getDateFin() && $dateFin >= $data->getDateFin()) || ($dateDebut >= $data->getDateDebut() && $dateFin <= $data->getDateFin())))
                        {
                            throw new HttpException(403, "Cet utilisateur est dèja affecté à un compte dans même periode");
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
