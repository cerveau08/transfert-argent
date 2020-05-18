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
public function __construct(TokenStorageInterface $tokenStorage, AffectationRepository $affect, PartenaireRepository $partenaireRepo){
    
    $this->tokenStorage = $tokenStorage;
    $this->affect = $affect;
    $this->partenaireRepo = $partenaireRepo;
}

public function __invoke(Affectation $data):Affectation
{  
$userConnect = $this->tokenStorage->getToken()->getUser();
if($userConnect->getRoles()[0] == "ROLE_ADMIN_SYSTEM" || $userConnect->getRoles()[0] == "ROLE_ADMIN" ||
  $userConnect->getRoles()[0] == "ROLE_CAISSIER" || $userConnect->getRoles()[0] == "ROLE_CAISSIER_PARTENAIRE"){   
    throw new HttpException("403", "Acces Refuse, Vous n'etes pas autorise à affecter un Compte Partenaire ");
    }else{  
    $data->setUserQuiAffecte($userConnect);
    }
    if($data->getUserQuiAffecte()->getRoles()[0] == "ROLE_PARTENAIRE" || $data->getUserQuiAffecte()->getRoles()[0] == "ROLE_ADMIN_PARTENAIRE")
    {
        $idPartenaire = $data->getUserQuiAffecte()->getPartenaire()->getId();
        $Partenaire = $this->partenaireRepo->ListUserToPartenaire($idPartenaire);
        $ListeUserPartenaire = $Partenaire->getUserPartenaire(); 
        $tabUserPartenaire = $ListeUserPartenaire->toArray();
    $ListeComptePartenaire = $Partenaire->getComptePartenaire();
    $tabComptePartenaire = $ListeComptePartenaire->toArray(); 
    }  
$startDate  = $data->getDateDebut()->format('Y-m-d'); //formater la Date de Debut de Periode recuperer
$endDate  = $data->getDateFin()->format('Y-m-d'); //formater la Date de Fin de Periode recuperer
$periode=$this->IntervalDate($startDate, $endDate); //Recuperer la function de Calcul Interval de Date Periode
if($periode){
if($data->getUserComptePartenaire()->getRoles()[0] == "ROLE_ADMIN_SYSTEM" || $data->getUserComptePartenaire()->getRoles()[0] =="ROLE_ADMIN" ||
   $data->getUserComptePartenaire()->getRoles()[0] == "ROLE_CAISSIER"){

    throw new HttpException("403", "Impossible d'affecter un Compte Partenaire aux utilisateurs du Systeme");

}elseif($data->getUserComptePartenaire()->getRoles()[0] == "ROLE_ADMIN_PARTENAIRE" || $data->getUserComptePartenaire()->getRoles()[0]== "ROLE_PARTENAIRE" )
{
    throw new HttpException("403", "Impossible d'affecter un Compte à l'Administrateur Général ou Admin Partenaire de l'Agence ".$data->getUserQuiAffecte()->getPartenaire()->getRegistreCommercial());
}elseif($userConnect->getPartenaire()->getNinea() != $data->getUserComptePartenaire()->getPartenaire()->getNinea()){

    throw new HttpException("403", "Cet utilisateur n'est pas un personnel de l'Agence ".$data->getUserQuiAffecte()->getPartenaire()->getRegistreCommercial());
}
//recuperer la liste des Comptes d'une agence via la connexion de l'utilisateur qui effetue l'affectation
$listeCompte = $data->getUserQuiAffecte()->getPartenaire()->getComptes();
$tabCompte = $listeCompte->toArray(); // convertir la PersistanceCollection du compte en Array
//parcourir la position de la liste des comptes à affecter 
for ($i=0; $i < count($tabCompte); $i++) {  
//Verifier Si ce Compte Sélectionner est partmis les Comptes de l'Agence
if($tabCompte[$i]->getPartenaire() == $data->getCompte()->getPartenaire())
     {
        $data->setCompte($data->getCompte());
 break;
     }else{

   throw new HttpException("404", "Ce Compte sous le numero de ". $data->getCompte()->getNumeroCompte(). " n'existe pas ou n'appartient pas à l'agence ".$data->getUserQuiAffecte()->getPartenaire()->getRegistreCommercial());       
     }   
}
//Verifier si l'utilisateur Courant est affecte a un autre Compte dans cette meme periode
 $AffectationComptes = $this->affect->findAll();
    for ($i=0; $i < count($AffectationComptes); $i++) {       
        $dateStart = $AffectationComptes[$i]->getDateDebut();
        $dateEnd = $AffectationComptes[$i]->getDateFin();
        $userAffectCompte = $AffectationComptes[$i]->getUserAffectCompte();
        if($dateStart == $data->getDateDebut() && $dateEnd == $data->getDateFin() && $userAffectCompte == $data->getUserComptePartenaire())
        {
            throw new Exception("Le Compte Nº: ".$data->getCompte()->getNumeroCompte()." est dejà affecté à l'utilisateur ".$data->getUserComptePartenaire()->getUsername()." dans cette meme période");
        }
    }
}else{
throw new Exception("La durée d'utilisation du Compte est à terme !!! Vous n'êtes plus affecté au Compte Nº: ".$data->getCompte()->getNumeroCompte());
}
 return $data;
}
//Function Calcul Interval entre deux dates pour determiner la Période d'affectation de Compte BY SON EXCELLENCE WADE
private function IntervalDate($dateDebut, $dateFin)
 {
    $currentDate = date('Y-m-d');
    $currentDate = date('Y-m-d', strtotime($currentDate)); 
    if(($currentDate >= $dateDebut) && ($currentDate <= $dateFin) ){  
        return true;
    }else{
         return false;
    }  
 }
}