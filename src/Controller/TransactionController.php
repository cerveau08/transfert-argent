<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Tarif;
use App\Entity\Compte;
use App\Entity\Partenaire;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AffectationRepository;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function envoi(Request $request, EntityManagerInterface $entityManager, AffectationRepository $repo)
    {
        $values = json_decode($request->getContent());
        if(isset($values->montant,$values->nomCompletE,$values->telephoneE))
        {
            $dateEnvoi = new \DateTime();
            $transaction = new Transaction(); 
            //recuperation du caissier qui envoie
            $userCompteE = $this->tokenStorage->getToken()->getUser();
            
           
            
            if($userCompteE->getRoles()[0] === "ROLE_CAISSIER_PARTENAIRE"){
                $compteEmetteur=$repo->findCompteAffectTo($userCompteE)[0]->getCompte();
                $transaction->setCompteEmetteur($compteEmetteur);
               // dd($compteEmetteur);
            }
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
           // dd($frais);
           // $total = $montantsaisie + $frais;
            // repartition des commissions 
            $taxeEtat = $frais * 0.4;
            $commissionSysteme = $frais * 0.3;
            $commissionE = $frais * 0.1;
            $commissionR = $frais * 0.2;

            $valeurEnvoi = $values->montant + $frais - $commissionE;
            //dd($valeurEnvoi);
            //Verifier le montant dispo
           
            // var_dump($comptes); die();
            if ($valeurEnvoi >= $compteEmetteur->getSolde()) {
                return $this->json([
                    'message1' => 'votre solde( ' . $compteEmetteur->getSolde() . ' ) ne vous permez pas d\'effectuer cette transaction'
                ]);
            }

            $NouveauSolde = ($compteEmetteur->getSolde()-$valeurEnvoi);
            $compteEmetteur->setSolde($NouveauSolde);
            //dd($NouveauSolde);
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
            // dd($transaction);
            $entityManager->flush();

        $data = [
                'status' => 201,
                'message' => 'vous avez envoyer '.$values->montant + $frais.' a '.$values->nomCompletR.'.Code de transaction '.$codes
            ];
            return new JsonResponse($data, 201);
        }
        $data = [
            'status' => 500,
            'message' => 'Vous devez rensseigner tous les champs'
        ];
        return new JsonResponse($data, 500);
    }

     /**
     * @Route("/retrait", name="transaction_retrait", methods={"POST"})
    */
    public function retrait(Request $request, EntityManagerInterface $entityManager, AffectationRepository $repo)
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
            //recuperation du caissier qui fait le retrait
            $userCompteR = $this->tokenStorage->getToken()->getUser();

            if($userCompteR->getRoles()[0] === "ROLE_CAISSIER_PARTENAIRE"){
                $compteRecepteur=$repo->findCompteAffectTo($userCompteR)[0]->getCompte();
                $code->setCompteRecepteur($compteRecepteur);
            }
            $code->setUserCompteR($userCompteR);
            $code->setDateRetrait($dateRetrait);
            $code->setTypePieceR($values->typePieceR);
            $code->setNumeroPieceR($values->numeroPieceR);
            $NouveauSolde = ($compteRecepteur->getSolde()+$code->getMontant()+$code->getCommisionR());
            $compteRecepteur->setSolde($NouveauSolde);
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
            'message' => 'Vous devez renseigner tous les champs'
        ];
        return new JsonResponse($data, 500);
    }
}