<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Entity\Partenaire;
use App\Entity\Affectation;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AffectationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/api")
 */
class ComptePartenaireController extends AbstractController
{
    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager, AffectationRepository $affect)
    {
        $this->tokenStorage = $tokenStorage;
        $this->affect = $affect;
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/compte", name="compte", methods={"GET"})
     */
    public function getAllCompte()
    {
        $repo = $this->getDoctrine()->getRepository(Compte::class);
        $comptes = $repo->findAll();
        
        $data = [];
        $i= 0;
        $userconnect = $this->tokenStorage->getToken()->getUser();
        
        $rolesUser = $userconnect->getRoles()[0];
      //  dd($userconnect);
        if($rolesUser ===  'ROLE_ADMIN_SYSTEM' || $rolesUser === 'ROLE_ADMIN')
        {
            foreach($comptes as $compte)
            {
                    $formatDate  = $compte->getDateCreation()->format('Y-m-d');
                    $data[$i]=$compte;
                    $i++;      
            }
        }
        elseif($rolesUser === 'ROLE_PARTENAIRE' || $rolesUser === 'ROLE_ADMIN_PARTENAIRE')
        {
            $nineauser = $userconnect->getPartenaire()->getNinea();   
            foreach($comptes as $compte)
            {
                if($nineauser === $compte->getPartenaire()->getNinea())
                {
                    $data[$i]=$compte;
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
     * @Route("/transaction", name="transaction", methods={"GET"})
     */
    public function getTransaction()
    {
        $repo = $this->getDoctrine()->getRepository(Transaction::class);
        $transactions = $repo->findAll();
        
        $data = [];
        $i= 0;
        $userconnect = $this->tokenStorage->getToken()->getUser();
        
        $rolesUser = $userconnect->getRoles()[0];
      //  dd($userconnect);
        if($rolesUser ===  'ROLE_ADMIN_SYSTEM' || $rolesUser === 'ROLE_ADMIN')
        {
            foreach($transactions as $transaction)
            {
                    $data[$i]=$transaction;
                    $i++;      
            }
        }
        elseif($rolesUser === 'ROLE_PARTENAIRE' || $rolesUser === 'ROLE_ADMIN_PARTENAIRE')
        {
            $nineauser = $userconnect->getPartenaire()->getNinea();   
            foreach($transactions as $transaction)
            {
               // dd($transaction);
                if($nineauser === $transaction->getCompteEmetteur()->getPartenaire()->getNinea() || $nineauser === $transaction->getCompteRecepteur()->getPartenaire()->getNinea())
                {
                    //dd($transaction);
                    $data[$i]=$transaction;
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
     * @Route("/partenaire", name="partenaire", methods={"GET"})
     */
    public function getAllPartenaire()
    {
        $repo = $this->getDoctrine()->getRepository(Partenaire::class);
        $partenaires = $repo->findAll();
        
        $data = [];
        $i= 0;
        $userconnect = $this->tokenStorage->getToken()->getUser();
        
        $rolesUser = $userconnect->getRoles()[0];
      //  dd($userconnect);
        if($rolesUser ===  'ROLE_ADMIN_SYSTEM' || $rolesUser === 'ROLE_ADMIN')
        {
            foreach($partenaires as $partenaire)
            {
                    $data[$i]=$partenaire;
                    $i++;      
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
     * @Route("/affectation", name="affectation", methods={"GET"})
     */
    public function getAffectation()
    {
        $repo = $this->getDoctrine()->getRepository(Affectation::class);
        $affectations = $repo->findAll();
        
        $data = [];
        $i= 0;
        $userconnect = $this->tokenStorage->getToken()->getUser();
        
        $rolesUser = $userconnect->getRoles()[0];
      //  dd($userconnect);
        if($rolesUser === 'ROLE_PARTENAIRE' || $rolesUser === 'ROLE_ADMIN_PARTENAIRE')
        {
            $nineauser = $userconnect->getPartenaire()->getNinea();   
            foreach($affectations as $affectation)
            {
                if($nineauser === $affectation->getCompte()->getPartenaire()->getNinea())
                {
                    $data[$i]=$affectation;
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
     * @Route("/retrait", name="transaction_retrait", methods={"POST"})
    */
    public function retrait(Request $request, EntityManagerInterface $entityManager)
    {
        $values = json_decode($request->getContent());
        $userC = $this->tokenStorage->getToken()->getUser();
            $role=$userConnecter->getRoles()[0];
        if(isset($values->numeroPieceR))
        {
            $dateRetrait = new \DateTime();
            $code = new Transaction(); 
           

             $code->setCode($values->code);
            // dd($values);
             $repositori = $this->entityManager->getRepository(Transaction::class);
             $code = $repositori->findOneByCode($values->code);
             if(!$code){
                return new Response('Ce code est invalide ',Response::HTTP_CREATED);
            }

                if($code->getStatus()=="retire" ){
                    return new Response('Le code est déja retiré',Response::HTTP_CREATED);
                }
            // dd($code);
            //recuperation du caissier qui envoie
            
            $code->setUserCompteR($userC);
            $code->setDateRetrait($dateRetrait);
            $code->setTypePieceR($values->typePieceR);
            $code->setNumeroPieceR($values->numeroPieceR);
            $comptes = $userC->getCompte();
            $NouveauSolde = ($comptes->getSolde()+$code->getMontant()+$code->getCommisionR());
            $comptes->setSolde($NouveauSolde);
             $code->setStatus('retire');
            $entityManager->persist($code);
            $entityManager->flush();

        $data = [
                'status' => 201,
                'message' => 'Le retrait est fait'
            ];
            return new JsonResponse($data, 201);
        }
        $data = [
            'status' => 500,
            'message' => 'Vous devez renseigner un login et un passwordet un ninea pour le partenaire, le numero de compte ainsi que le montant a deposer'
        ];
        return new JsonResponse($data, 500);
    }
}
