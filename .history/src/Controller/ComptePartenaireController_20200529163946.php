<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Entity\Transaction;
use App\Entity\Partenaire;
use App\Entity\Affectation;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/api")
 */
class ComptePartenaireController extends AbstractController
{
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
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
    public function getAllTransaction()
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
                    dd($transaction);
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
                if($nineauser === $affectation->getComptegetPartenaire()->getNinea())
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
}
