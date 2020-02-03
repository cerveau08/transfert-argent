<?php

namespace App\Controller;

use App\Entity\Profil;
use App\Entity\User;
use App\Entity\Depot;
use App\Entity\Compte;
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
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
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
            // $partenaire->setDateContrat($dateJours);
            // $partenaire->setUsers($user);

            $entityManager->persist($partenaire);
            $entityManager->flush();

            ####    GENERATION DU NUMERO DE COMPTE  ####
            $annee = Date('y');
            $cpt = $this->getLastCompte();
            $long = strlen($cpt);
            $ninea2 = substr($partenaire->getNinea() , -2);
            $NumeroCompte = str_pad("MA".$annee.$ninea2, 11-$long, "0").$cpt;
                    #####   COMPTE    ######
            // recuperer de l'utilisateur qui cree le compte et y effectue un depot initial
            $userCreateur = $this->tokenStorage->getToken()->getUser();
            $compte->setNumeroCompte($NumeroCompte);
            $compte->setSolde(0);
            $compte->setDateCreation($dateCreation);
            $compte->getUsers($userCreateur);
            $compte->setPartenaire($partenaire);  

            $entityManager->persist($compte);
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
    // public function compte_PartenaireExistent(Request $request, EntityManagerInterface $entityManager)
    // {
    //     $values = json_decode($request->getContent());
    //     if(isset($values->ninea,$values->montant))
    //     {
    //         // je controle si l'utilisateur a le droit de creer un compte (appel CompteVoter)
    //         $this->denyAccessUnlessGranted('POST_EDIT',$this->getUser());
    //         $ReposProprietaire = $this->getDoctrine()->getRepository(User::class);
    //             // recuperer de l'utilisateur proprietaire du compte
    //             $proprietaire = $ReposProprietaire->findOneByNinea($values->ninea);
    //         if ($proprietaire) 
    //         {
    //             if ($values->montant > 0) 
    //             {
    //                 $dateJours = new \DateTime();
    //                 $depot = new Depot();
    //                 $compte = new Compte();
    //                 #####   COMPTE    ######
                
    //                 // recuperer de l'utilisateur qui cree le compte et y effectue un depot initial
    //                 $userCreateur = $this->tokenStorage->getToken()->getUser();

    //                 ####    GENERATION DU NUMERO DE COMPTE  ####
    //                 $annee = Date('y');
    //                 $cpt = $this->getLastCopmte();
    //                 $long = strlen($cpt);
    //                 $ninea2 = substr($proprietaire->getNinea(), -2);
    //                 $NumCompte = str_pad("KH".$annee.$ninea2, 11-$long, "0").$cpt;
    //                 $compte->setNumero($NumCompte);
    //                 $compte->setSolde($values->montant);
    //                 $compte->setDateCreation($dateJours);
    //                 $compte->setUserCreateur($userCreateur);
    //                 $compte->setProprietaire($proprietaire);

    //                 $entityManager->persist($compte);
    //                 $entityManager->flush();
    //                 #####   DEPOT    ######
    //                 $ReposCompte = $this->getDoctrine()->getRepository(Compte::class);
    //                 $compteDepos = $ReposCompte->findOneByNumero($NumCompte);
    //                 $depot->setDateDepot($dateJours);
    //                 $depot->setMontant($values->montant);
    //                 $depot->setUserDepot($userCreateur);
    //                 $depot->setCompte($compteDepos);

    //                 $entityManager->persist($depot);
    //                 $entityManager->flush();

//                 $data = [
    //                     'status' => 201,
    //                     'message' => 'Le compte du partenaire est bien cree avec un depot initia de: '.$values->montant
    //                     ];
    //                 return new JsonResponse($data, 201);
    //             }
    //             $data = [
    //                 'status' => 500,
    //                 'message' => 'Veuillez saisir un montant de depot valide'
    //                 ];
    //                 return new JsonResponse($data, 500);
    //         }
    //         $data = [
    //             'status' => 500,
    //             'message' => 'Desole le NINEA saisie n est ratache a aucun partenaire'
    //             ];
    //             return new JsonResponse($data, 500);
    //     }
    //     $data = [
    //         'status' => 500,
    //         'message' => 'Vous devez renseigner le ninea du partenaire, le numero de compte ainsi que le montant a deposer'
    //         ];
    //         return new JsonResponse($data, 500);
    // }    

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