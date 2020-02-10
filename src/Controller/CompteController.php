<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Depot;
use App\Entity\Compte;
use App\Entity\Profil;
use App\Entity\Contrat;
use App\Entity\Partenaire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

    /** 
     * @Route("/api")
    */

class CompteController extends AbstractController
{
    private $tokenStorage;
    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
    }

     /** 
     * @Route("/newcomptep", name="creation_compte_NewPartenaire", methods={"POST"})
     */
    public function compteNew_Partenaire(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $userPasswordEncoder)
    {
    //     $a = $this->denyAccessUnlessGranted('POST', $this->getUser());
    //    // dd($a);
        $values = json_decode($request->getContent());
        if(isset($values->email,$values->password,$values->ninea,$values->montant,$values->nomComplet,$values->adresse,$values->telephone,$values->registreCommercial))
        {
            $dateCreation = new \DateTime();
            $depot = new Depot();
            $compte = new Compte();                     
            $user = new User();
            $partenaire = new Partenaire();
            $contrat =new Contrat();  
            
            $userCreateur = $this->tokenStorage->getToken()->getUser();
            // AFFECTATION DES VALEURS AUX DIFFERENTS TABLE
                    #####   USER    ######
            $roleRepo = $this->getDoctrine()->getRepository(Profil::class);
            $role = $roleRepo->find($values->profil);
            $user->setEmail($values->email);
            $user->setUsername($values->username);
            $user->setPassword($userPasswordEncoder->encodePassword($user, $values->password));
            $user->setProfil($role);
            $user->setPartenaire($partenaire);
            
            $entityManager->persist($user);
          //  $entityManager->flush();

            $partenaire->setNinea($values->ninea);
            $partenaire->setRegistreCommercial($values->registreCommercial);
            $partenaire->setNomComplet($values->nomComplet);
            $partenaire->setAdresse($values->adresse);
            $partenaire->setTelephone($values->telephone);
            $partenaire->setUserCreateur($userCreateur);
            // $partenaire->setDateContrat($dateJours);
            // $partenaire->setUsers($user);

            $entityManager->persist($partenaire);
            $entityManager->flush();

            ####    GENERATION DU NUMERO DE COMPTE  ####
            $annee = Date('y');
            $cpt = $this->getLastCompte();
            $long = strlen($cpt);
            $ninea2 = substr($partenaire->getNinea() , -2);
            $numeroCompte = str_pad("MA".$annee.$ninea2, 11-$long, "0").$cpt;
                    #####   COMPTE    ######
            // recuperer de l'utilisateur qui cree le compte et y effectue un depot initial
           
            $compte->setNumeroCompte($numeroCompte);
            $compte->setSolde(0);
            $compte->setDateCreation($dateCreation);
            $compte->setUserCreateur($userCreateur);
            $compte->setPartenaire($partenaire);  

            $entityManager->persist($compte);
            $entityManager->flush();

            $info = "Contrat:Entre les soussignés:
            Malick Coly et ".$partenaire->getNomComplet().",immatriculé sous le ninea ".$partenaire->getNinea()."
            du registre commercial ".$partenaire->getRegistreCommercial().",demeurant a ".$partenaire->getAdresse().". Son numéro téléphone est ".$partenaire->getTelephone()."
            ci-après dénommé le partenariat.";
             
            $comptespartenaires = "Les Comptes du Partenaires:".$compte->getNumeroCompte()."
            
            Il a été arrêté et convenu ce qui suit :";
             
            $article1 = "Article 1 : Engagement
            Le partenariat a demare à compter du .
            Le partenaire déclare être, à compter de la date effective de partenariat, libre de tout engagement de nature à faire obstacle à l’exécution du présent contrat.
             ";
            $article2 = "Article 2 : Fonctions et attributions
            Le partenaire est engagé en qualité d’utiliser notre plateforme pour créer des comptes et effectuer des transactions sur ses comptes
           ";
            $article3 = "Article 3 : Lieu de travail
            Le partenaire exercera ses fonctions dans les locaux qui lui convient
             ";
            $article4 = "Article 4 : En contrepartie, le partenaire percevra une rémunération sur commission pour chaque transaction effectuée
            commission Envoi = (frais de transfert X 10)/100
            commission Retrait = (frais de transfert X 20)/100
             ";
            $article5 = "Article 5 : Préavis
            Chacune des parties a la possibilité de rompre le présent contrat dans les conditions prévues par la loi, sous réserve de respecter le préavis de :
             • Malick Coly pour le blocage et l’annulation";
            $signatures = "Fait en double exemplaire au Parcelles Assainies, le .
            Signature à faire précéder de la mention manuscrite lu et approuvé
            Directeur Général de Goukodi,                                    	Le partenaire.
            ";
            $contrat->setInformation($info);
            $contrat->setComptes($comptespartenaires);
            $contrat->setArticle1($article1);
            $contrat->setArticle2($article2);
            $contrat->setArticle3($article3);
            $contrat->setArticle4($article4);
            $contrat->setArticle5($article5);
            $contrat->setSignature($signatures);
            $contrat->setPartenaire($partenaire);
            $contrat->setDateCreation($dateCreation);
            $entityManager->persist($contrat);
            $entityManager->flush();


                    #####   DEPOT    ######
            $depot->setDateDepot($dateCreation);
            $depot->setMontant($values->montant);
            $depot->setCaissierAdd($userCreateur);
            $depot->setCompte($compte);

            $entityManager->persist($depot);
            $entityManager->flush();

            ####    MIS A JOUR DU SOLDE DE COMPTE   ####
            $NouveauSolde = ($values->montant+$compte->getSolde());
            $compte->setSolde($NouveauSolde);
            $entityManager->persist($compte);
            $entityManager->flush();

        $data = [
                'status' => 201,
                'message' => 'Le compte du partenaire est bien cree avec un depot initia de: '.$values->montant
            ];
            return new JsonResponse($data, 201);
        }
        $data = [
            'status' => 500,
            'message' => 'Vous devez renseigner un login et un passwordet un ninea pour le partenaire, le numero de compte ainsi que le montant a deposer'
        ];
        return new JsonResponse($data, 500);
    }
    
    /**
     * @Route("/compte_PartenaireExistent", name="creation_compte_PartenaireExistent", methods={"POST"})
     */
     public function compte_PartenaireExistent(Request $request, EntityManagerInterface $entityManager)
     {
         $values = json_decode($request->getContent());
         if(isset($values->ninea,$values->montant))
         {
             // je controle si l'utilisateur a le droit de creer un compte (appel CompteVoter)
            // $this->denyAccessUnlessGranted('POST_EDIT',$this->getUser());

             $ninea = new Partenaire();
             $ninea->setNinea($values->ninea);
            // dd($values);
             $repositori = $this->entityManager->getRepository(Partenaire::class);
             $ninea = $repositori->findOneByNinea($values->ninea);
            // dd($ninea);
             if ($ninea) 
             {
                 if ($values->montant > 0) 
                 {
                     $dateJours = new \DateTime();
                     $depot = new Depot();
                     $compte = new Compte();
                     #####   COMPTE    ######
                
                     // recuperer de l'utilisateur qui cree le compte et y effectue un depot initial
                     $userCreateur = $this->tokenStorage->getToken()->getUser();

                     ####    GENERATION DU NUMERO DE COMPTE  ####
                     $annee = Date('y');
                     $cpt = $this->getLastCompte();
                     $long = strlen($cpt);
                     $ninea2 = substr($ninea->getNinea(), -2);
                     $NumCompte = str_pad("MA".$annee.$ninea2, 11-$long, "0").$cpt;
                     $compte->setNumeroCompte($NumCompte);
                     $compte->setSolde($values->montant);
                     $compte->setDateCreation($dateJours);
                     $compte->setUserCreateur($userCreateur);
                     $compte->setPartenaire($ninea);

                     $entityManager->persist($compte);
                     $entityManager->flush();
                     #####   DEPOT    ######
                    // $ReposCompte = $this->getDoctrine()->getRepository(Compte::class);
                    // $compteDepos = $ReposCompte->findOneBynumeroCompte($NumCompte);
                     $depot->setDateDepot($dateJours);
                     $depot->setMontant($values->montant);
                     $depot->setCaissierAdd($userCreateur);
                     $depot->setCompte($compte);

                     $entityManager->persist($depot);
                     $entityManager->flush();

                  $data = [
                         'status' => 201,
                         'message' => 'Le compte du partenaire est bien cree avec un depot initia de: '.$values->montant
                         ];
                     return new JsonResponse($data, 201);
                 }
                 $data = [
                     'status' => 500,
                     'message' => 'Veuillez saisir un montant de depot valide'
                     ];
                     return new JsonResponse($data, 500);
             }
             $data = [
                 'status' => 500,
                 'message' => 'Desole le NINEA saisie n est ratache a aucun partenaire'
                 ];
                 return new JsonResponse($data, 500);
         }
         $data = [
             'status' => 500,
             'message' => 'Vous devez renseigner le ninea du partenaire, le numero de compte ainsi que le montant a deposer'
             ];
             return new JsonResponse($data, 500);
     }    

    public function getLastCompte(){
        $ripo = $this->getDoctrine()->getRepository(Compte::class);
        $compte = $ripo->findBy([], ['id'=>'DESC']);
        if(!$compte){
            $cpt = 1;
        }else{
            $cpt = ($compte[0]->getId()+1);
        }
        return $cpt;
      }
}