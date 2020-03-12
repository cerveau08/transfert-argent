<?php


namespace App\DataPersister;

use DateTime;
use App\Entity\Depot;
use App\Entity\Compte;
use App\Entity\Contrat;
use App\Entity\Partenaire;
use App\Repository\ProfilRepository;
use App\Repository\TermesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Config\Definition\Exception\Exception;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CompteDataPersister implements ContextAwareDataPersisterInterface
{
    
    private $entityManager;
    public function __construct(TermesRepository $term, ProfilRepository $repo, EntityManagerInterface $entityManager,TokenStorageInterface $tokenStorage, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
        $this->repo = $repo;
        $this->term = $term;
    }
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Compte;
    }
    public function persist($data, array $context = [])
    {
          
    $userCreateur=$this->tokenStorage->getToken()->getUser();
    $iduser=$data->getPartenaire()->getId();
    $montant=($data->getDepot()[count($data->getDepot())-1]->getMontant());
    $date=date_format($data->getDateCreation(),"Yms");
    $num=rand(1000, 9999);
  
    if($iduser == null){
    $userPass=$data->getPartenaire()->getUserComptePartenaire()[0]->getPassword();
    //le user partenaire
    $user=$data->getPartenaire()->getUserComptePartenaire()[0];
    $user->setPassword($this->userPasswordEncoder->encodePassword($user, $userPass));
    $user->setProfil($this->repo->findByLibelle("ROLE_PARTENAIRE")[0]);
    $data->getPartenaire()->setUserCreateur($userCreateur);
   }//dd($user);
   //dd($data->getDepots());
    if($data->getDepot()[0]->getMontant() >= 500000){
        
        $data->setSolde($montant);
        $data->setUserCreateur($userCreateur);
        $data->getDepot()[0]->setCaissierAdd($userCreateur);
        $data->setNumeroCompte($date.$num);
    }else{
        throw new Exception("Le montant doit etre superieur ou égale à 500.000");
    }           
                $this->entityManager->persist($data);
                $this->entityManager->flush();
                if($iduser == null) {
                    $contrat = new Contrat();
                    $contrat->setPartenaire($data->getPartenaire());
                    $contrat->setDateCreation(new DateTime());
                    $termes = $this->term->findAll()[0]->getTerme();
                    $contrat->setInformation($termes);
                    $this->entityManager->persist($contrat);
                    $this->entityManager->flush();
                    $data=$data->getPartenaire();
                   return $contrat->genContrat($data,$termes);
      }
    }
   
    public function remove($data, array $context = [])
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}