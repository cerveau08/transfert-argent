<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Entity\Transaction;
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
     * @Route("/transaction", name="compte", methods={"GET"})
     */
    public function getAllTransaction()
    {
        $repo = $this->getDoctrine()->getRepository(Transaction::class);
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
     * @Route("/partenaire", name="compte", methods={"GET"})
     */
    public function getAllPartenaire()
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
}
