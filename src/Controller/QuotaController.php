<?php

namespace App\Controller;

use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/api")
 */
class QuotaController extends AbstractController
{
    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/quotasE", name="quotasE", methods={"GET"})
     */
    public function listerPartE(Request $request)
    {
        $data = [];
        $i= 0;
        $userconnect = $this->tokenStorage->getToken()->getUser();
        $rolesUser = $userconnect->getRoles()[0];
        if($rolesUser ===  'ROLE_ADMIN_SYSTEM' || $rolesUser === 'ROLE_ADMIN')
         {
          $values = json_decode($request->getContent());
          $ReposCompte = $this->entityManager->getRepository(Transaction::class);
          $partes = $ReposCompte->getPartCompteE($values->compteEmetteur);
            foreach($partes as $part)
            {
              $data[$i]=$part;
                    $i++;      
            }
         }else
         {
          $data = [
              'status' => 401,
              'message' => 'Désolé access non autorisé !!!'
              ];
          
         }
         return $this->json($data, 200);
  }    
  /**
     * @Route("/quotaE", name="quotaE", methods={"PUT"})
     */
    public function part(Request $request)
    {
        $i= 0;
        $userconnect = $this->tokenStorage->getToken()->getUser();
       //  dd($ReposCompte);
        $rolesUser = $userconnect->getRoles()[0];
        if($rolesUser ===  'ROLE_ADMIN_SYSTEM' || $rolesUser === 'ROLE_ADMIN')
       // dd($compte);
         {
            $values = json_decode($request->getContent());
            // dd($values);
          //  $transaction = new Transaction();                     
          // $compte->setNumeroCompte($values->compte);
            $ReposCompte = $this->entityManager->getRepository(Transaction::class);
          // $parts = $ReposCompte->findAll();
            $ReposCompte->updatePartCompteE($values->compteEmetteur);
          // dd($partes);
                  $sommeE = $ReposCompte->getSommePartEmetteur($values->compteEmetteur);
                  dd($sommeE);
                  
                   $data = [
                     'status'=>200,
                     'message'=>'La somme total que vous devez avoir est '.$sommeE
                   ];                 
         }else
         {
          $data = [
              'status' => 401,
              'message' => 'Désolé access non autorisé !!!'
              ];
         }
            return $this->json($data, 200);
  }    

  /**
     * @Route("/quotasR", name="quotasR", methods={"GET"})
     */
    public function listerPartR(Request $request)
    {
        $data = [];
        $i= 0;
        $userconnect = $this->tokenStorage->getToken()->getUser();
        $rolesUser = $userconnect->getRoles()[0];
        if($rolesUser ===  'ROLE_ADMIN_SYSTEM' || $rolesUser === 'ROLE_ADMIN')
         {
          $values = json_decode($request->getContent());
          $ReposCompte = $this->entityManager->getRepository(Transaction::class);
          $partes = $ReposCompte->getPartCompteR($values->compteRecepteur);
            foreach($partes as $part)
            {
              $data[$i]=$part;
                    $i++;      
            }
         }else
         {
          $data = [
              'status' => 401,
              'message' => 'Désolé access non autorisé !!!'
              ];
          
         }
         return $this->json($data, 200);
  } 

  /**
     * @Route("/quotaR", name="quotaR", methods={"PUT"})
     */
    public function partR(Request $request)
    {
        $i= 0;
        $userconnect = $this->tokenStorage->getToken()->getUser();
       //  dd($ReposCompte);
        $rolesUser = $userconnect->getRoles()[0];
        if($rolesUser ===  'ROLE_ADMIN_SYSTEM' || $rolesUser === 'ROLE_ADMIN')
       // dd($compte);
         {
            $values = json_decode($request->getContent());
            // dd($values);
          //  $transaction = new Transaction();                     
          // $compte->setNumeroCompte($values->compte);
            $ReposCompte = $this->entityManager->getRepository(Transaction::class);
          // $parts = $ReposCompte->findAll();
            $ReposCompte->updatePartCompteR($values->idR);
          // dd($partes);
                  $sommeR = $ReposCompte->getSommePartRecepteur($values->idR);
                  dd($sommeR);
                  
                   $data = [
                     'status'=>200,
                     'message'=>'La somme total que vous devez avoir est '.$sommeR
                   ];                 
         }else
         {
          $data = [
              'status' => 401,
              'message' => 'Désolé access non autorisé !!!'
              ];
         }
            return $this->json($data, 200);
  }    
}
