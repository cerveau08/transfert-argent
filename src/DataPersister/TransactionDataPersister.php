<?php

namespace App\DataPersister;

use App\Entity\Tarif;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AffectationRepository;
use App\Repository\TransactionRepository;
use Symfony\Component\Config\Definition\Exception\Exception;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TransactionDataPersister implements ContextAwareDataPersisterInterface
{
    private $entityManager;
    public function __construct(TransactionRepository $transfert,AffectationRepository $affect, EntityManagerInterface $entityManager,TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
        $this->affect = $affect;
        $this->transfert = $transfert;
    }
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Transaction;
        // TODO: Implement supports() method.
    }
    public function persist($data, array $context = [])
    {
             $userConnecter=$this->tokenStorage->getToken()->getUser();
             $role=$userConnecter->getRoles()[0];
             $leCode = $this->transfert->findOneByCode($data->getCode());
            // dd($leCode);
             if($leCode == null){
                if($role == "ROLE_CAISSIER_PARTENAIRE"){
                    $compteEmetteur=$this->affect->findCompteAffectTo($userConnecter)[0]->getCompte();
                    $data->setCompteEmetteur($compteEmetteur);
                }
                $data->setUserCompteE($userConnecter);
                $dateEnvoi = new \DateTime();
                $data->setDateEnvoi($dateEnvoi);
                //Gestion des Frais
                $montant=$data->getMontant();
                $repository = $this->entityManager->getRepository(Tarif::class);
                $commission = $repository->findAll();
                //dd($commission);
                foreach ($commission as $value) {
                    $value->getBorneInf();
                    $value->getBorneSup();
                    $value->getFrais();

                    if ($montant >= $value->getBorneInf() &&  $montant <= $value->getBorneSup()) {
                        $frais = $value->getFrais();
                    }  
                }
                $taxeEtat = $frais * 0.4;
                $commissionS = $frais * 0.3;
                $commissionE = $frais * 0.1;
                $commissionR = $frais * 0.2;

                $data->setTaxeEtat($taxeEtat);
                $data->setCommissionSysteme($commissionS);
                $data->setCommissionE($commissionE);
                $data->setCommisionR($commissionR);
                
                $m = "GO";
                $a = rand(1000, 9999);
                $z = "CE";
                $b = rand(1000, 9999);
                $codes = $m . $a . $z .$b;
                $data->setFrais($frais);
                $data->setCode($codes);

                $valeurEnvoi = $montant + $frais - $commissionE;
                //dd($valeurEnvoi);
                //Verifier le montant dispo
            
                // var_dump($comptes); die();
                if ($valeurEnvoi <= $compteEmetteur->getSolde()) {
                    $m = "GO";
                    $a = rand(1000, 9999);
                    $z = "CE";
                    $b = rand(1000, 9999);
                    $codes = $m . $a . $z .$b;
                    $data->setFrais($frais);
                    $data->setCode($codes);
                    $NouveauSolde = ($compteEmetteur->getSolde()-$valeurEnvoi);
                    $compteEmetteur->setSolde($NouveauSolde);
                    $data->setStatus('envoye');
                }else{
                    throw new Exception("Le Solde de votre compte ne vous permet pas d'envoyer cette somme");
                }
                $this->entityManager->persist($data);
                $this->entityManager->flush();
            }else{
                if($role == "ROLE_CAISSIER_PARTENAIRE"){
                    $compteRecepteur=$this->affect->findCompteAffectTo($userConnecter)[0]->getCompte();
                    $leCode->setCompteRecepteur($compteRecepteur);
                }
                $NouveauSolde = ($compteRecepteur->getSolde()+$data->getMontant()+$data->getCommisionR());
                $compteRecepteur->setSolde($NouveauSolde);
                $leCode->setStatus('retirer');
                $leCode->setUserCompteR($userConnecter);
                $dateRetrait = new \DateTime();
                $leCode->setDateRetrait($dateRetrait);

                $this->entityManager->persist($leCode);
                $this->entityManager->flush();
            }     
       
    }
   
    public function remove($data, array $context = [])
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}