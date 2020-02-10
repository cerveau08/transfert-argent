<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Tarif;
use App\Entity\Compte;
use App\Entity\Partenaire;
use App\Entity\Transaction;
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
           

            $montantsaisie=$transaction->setMontant($values->montant);
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
}