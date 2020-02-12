<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Tarif;
use App\Entity\Compte;
use App\Entity\Partenaire;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/api")
 */
class TransactionController extends AbstractController
{
    
    private $tokenStorage;
    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/envoie", name="transaction_envoi", methods={"POST"})
    */
    public function envoi(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $values = json_decode($request->getContent());
        if(isset($values->montant,$values->nomCompletE,$values->telephoneE))
        {
            $dateEnvoi = new \DateTime();
            $compte = new Compte();                     
            $user = new User();
            $transaction = new Transaction(); 
            //recuperation du caissier qui envoie
            $userCompteE = $this->tokenStorage->getToken()->getUser();
            $transaction->setUserCompteE($userCompteE);
           

            $transaction->setMontant($values->montant);
           // dd($montantsaisie);
            // verifier les frais  correspondant au montant
            // recuperer la valeur du frais
            $repository = $this->getDoctrine()->getRepository(Tarif::class);
            $commission = $repository->findAll();
            //dd($commission);
            foreach ($commission as $value) {
                $value->getBorneInf();
                $value->getBorneSup();
                $value->getFrais();

                if ($values->montant >= $value->getBorneInf() &&  $values->montant <= $value->getBorneSup()) {
                    $frais = $value->getFrais();
                }
                
            }
            
           // $total = $montantsaisie + $frais;
            // repartition des commissions 
            $taxeEtat = $frais * 0.4;
            $commissionSysteme = $frais * 0.3;
            $commissionE = $frais * 0.1;
            $commissionR = $frais * 0.2;

            $valeurEnvoi = $values->montant + $commissionE;
            //dd($valeurEnvoi);
            //Verifier le montant dispo
            $comptes = $userCompteE->getCompte();
            // var_dump($comptes); die();
            if ($valeurEnvoi >= $comptes->getSolde()) {
                return $this->json([
                    'message1' => 'votre solde( ' . $comptes->getSolde() . ' ) ne vous permez pas d\'effectuer cette transaction'
                ]);
            }

            $NouveauSolde = ($comptes->getSolde()-$values->montant+$commissionE);
            $comptes->setSolde($NouveauSolde);

             //creation du code de transaction
             $m = "GO";
             $a = rand(1000, 9999);
             $z = "CE";
             $b = rand(1000, 9999);
             $codes = $m . $a . $z .$b;
             $transaction->setFrais($frais);
             $transaction->setCode($codes);
             $transaction->setTypePieceE($values->typePieceE);
             $transaction->setNumeroPieceE($values->numeroPieceE);
             $transaction->setNomCompletE($values->nomCompletE); 
             $transaction->setTelephoneE($values->telephoneE);
             $transaction->setTelephoneR($values->telephoneR);
             $transaction->setNomCompletR($values->nomCompletR); 
             $transaction->setDateEnvoi($dateEnvoi);
             $transaction->setTaxeEtat($taxeEtat);
             $transaction->setCommissionSysteme($commissionSysteme);
             $transaction->setCommissionE($commissionE);
             $transaction->setCommisionR($commissionR);
             $transaction->setStatus('envoye');
             $entityManager->persist($transaction);
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
     * @Route("/retrait", name="transaction_retrait", methods={"POST"})
    */
    public function retrait(Request $request, EntityManagerInterface $entityManager)
    {
        $values = json_decode($request->getContent());
        if(isset($values->numeroPieceR))
        {
            $dateRetrait = new \DateTime();
            $code = new Transaction(); 
           /* $codes=$transaction->getCode();

            $code=$trans->findOneBy(['code'=>$codes]);
                    // var_dump($code); die();
            //$c=$code->getCode();
            
           //var_dump($code->getCommissionRetrait());  die();
            //var_dump( $this->getUser()->getCompte()->getSolde());  die();
           dd($code);
                if(!$code){
                    return new Response('Ce code est invalide ',Response::HTTP_CREATED);
                }
                    $statut=$code->getStatus();
    
                    if($code->getCode()==$codes && $statut=="retire" ){
                        return new Response('Le code est déja retiré',Response::HTTP_CREATED);
                    }
                    */
                   
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
            $userCompteR = $this->tokenStorage->getToken()->getUser();
            $code->setUserCompteR($userCompteR);
            $code->setDateRetrait($dateRetrait);
            $code->setTypePieceR($values->typePieceR);
            $code->setNumeroPieceR($values->numeroPieceR);
            $comptes = $userCompteR->getCompte();
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