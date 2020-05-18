<?php

namespace App\Controller;

use DateTime;
use App\Entity\Affectation;
use App\Repository\AffectationRepository;
use App\Repository\CompteRepository;
use App\Repository\PartenaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class AffectationController {
    public function __construct(TokenStorageInterface $tokenStorage, AffectationRepository $affectCompteRepo){

        $this->tokenStorage = $tokenStorage;
        $this->affectCompteRepo = $affectCompteRepo;
    }

    
    public function __invoke(Affectation $data):Affectation
    { 
        // condition pour tester lesutilisateurs qui ont l'autorisation d'aafecter un compte

        $userConect = $this->tokenStorage->getToken()->getUser();
       
        if ($userConect->getRoles()[0] == "ROLE_ADMIN_PARTENAIRE" || 
            $userConect->getRoles()[0] == "ROLE_PARTENAIRE")
        {
    
            $data->setUserAfect($userConect);

//Récupération date début date fin

            $dateDebut = $data->getDateDebut()->format('Y-m-d'); //Récupération date début et formatage
            $dateFin = $data->getDateFin()->format('Y-m-d'); //Récupération date fin
            $dateCourent = date('Y-m-d'); // Récupération date systéme 
            
            //comparaison pour savoir le la date courante est compris entre la date de début et la detae de fin

                if ($dateDebut <= $dateCourent && $dateCourent <= $dateFin) 
                {
                    //condition portant sur les utilisateurs qui peuvent bénéficier d'une affectation de compte

                    if ($data->getUserAfecteCompte()->getRoles()[0] == "ROLE_ADMIN_SYSTEME" ||
                        $data->getUserAfecteCompte()->getRoles()[0] == "ROLE_CAISSIER" ||
                        $data->getUserAfecteCompte()->getRoles()[0] == "ROLE_ADMIN" )
                    {
                        throw new HttpException(403, "cet utilisateur ne peut pas étre affecter à un compte");

                        //l'utilisateur doit qu'on affecte le compte doit appartenir aux agence que l'utilisateur qui lui affecte le compte

                        }elseif ($data->getUserAfecteCompte()->getPartenaire()->getNinea() != $data->getUserAfect()->getPartenaire()->getNinea()) {
                        
                            throw new HttpException(403, "cet utilisateur n'appartient pas à cette Agence ");
  
                             }
                                $listcompte = $data->getUserAfect()->getPartenaire()->getComptes();
                                $tabCompte = $listcompte->toArray();

                                for ($i=0; $i < count($tabCompte) ; $i++) { 
                                   if ( $tabCompte[$i]->getPartenaire() == $data->getCompte()->getPartenaire()) { 
                                      
                                    $data->setCompte($data->getCompte());
                                   }else{
                                    throw new HttpException(403, "le partenaire ne dispose pas de ce compte");
                                   }
                                }

                        //Controler si userPartenaire courant est affecte a un compte compte dans cette mm periode 

                          $affectCompte = $this->affectCompteRepo->findAll();

                          for ($i=0; $i <count($affectCompte) ; $i++) { 
                              $dateDebut = $affectCompte[$i]->getDateDebut();
                              $dateFin = $affectCompte[$i]->getDateFin();
                              $userAffect = $affectCompte[$i]->getUserAfecteCompte();
                            
                             if ($userAffect == $data->getUserAfecteCompte() &&( $dateDebut == $data->getDateDebut()) ||  $dateFin == $data->getDateFin() )
                              {
                                throw new HttpException(403, "Cet utilisateur est dèja affecté à un compte dans même periode");
                             }
                          }
    
                }else {

                    throw new HttpException(403, "la durée d'utilisation de ce compte est terminé");
                }

            }else{
                
             throw new HttpException(403, "vous n'étes pas autorisé à ce service veiullez vous rapprocher de vos supérieur");
             
        }

        return $data;
        
    }
}