<?php

namespace App\DataPersister;

use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Affectation;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;



class AffectationDataPersister implements ContextAwareDataPersisterInterface
{
    
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage,UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Affectation;
    }
    public function persist($data, array $context = [])
    {
       
        // condition pour tester lesutilisateurs qui ont l'autorisation d'aafecter un compte

        $userConect = $this->tokenStorage->getToken()->getUser();
        $nineauser = $userConect->getPartenaire()->getNinea();
        if ($userConect->getRoles()[0] == "ROLE_ADMIN_PARTENAIRE" || 
            $userConect->getRoles()[0] == "ROLE_PARTENAIRE")
        {
    
            $data->setUserQuiAffecte($userConect);

//Récupération date début date fin
           // dd($data->getDateDebut());
            $dateDebut = $data->getDateDebut()->format('Y-m-d'); //Récupération date début et formatage
            $dateFin = $data->getDateFin()->format('Y-m-d'); //Récupération date fin
            $dateCourent = date('Y-m-d'); // Récupération date systéme 
            
            //comparaison pour savoir le la date courante est compris entre la date de début et la detae de fin

                if ($dateDebut <= $dateCourent && $dateCourent <= $dateFin) 
                {
                    //condition portant sur les utilisateurs qui peuvent bénéficier d'une affectation de compte
                    //dd($data->getUserComptePartenaire()->getPartenaire()->getNinea());
                    if ($data->getUserComptePartenaire()->getRoles()[0] == "ROLE_ADMIN_SYSTEME" ||
                        $data->getUserComptePartenaire()->getRoles()[0] == "ROLE_CAISSIER" ||
                        $data->getUserComptePartenaire()->getRoles()[0] == "ROLE_ADMIN" )
                    {
                        throw new HttpException(403, "cet utilisateur ne peut pas étre affecter à un compte");

                        //l'utilisateur doit qu'on affecte le compte doit appartenir aux agence que l'utilisateur qui lui affecte le compte
                    
                        }elseif ($data->getUserComptePartenaire()->getPartenaire()->getNinea() != $nineauser) {
                        
                            throw new HttpException(403, "cet utilisateur n'appartient pas à cette Agence ");

                            }
                            
                                $listcompte = $data->getUserQuiAffecte()->getPartenaire()->getComptes();
                                $tabCompte = $listcompte->toArray();

                                for ($i=0; $i < count($tabCompte) ; $i++) { 
                                    if ( $tabCompte[$i]->getPartenaire() == $data->getCompte()->getPartenaire()) { 
                                        $data->setCompte($data->getCompte());
                                    }else{
                                    throw new HttpException(403, "le partenaire ne dispose pas de ce compte");
                                }
                                }

                        //Controler si userPartenaire courant est affecte a un compte compte dans cette mm periode 

                        $affectCompte = $this->affectCompteRepo->findAll();

                        for ($i=0; $i <count($affectCompte) ; $i++) { 
                            $dateDebut = $affectCompte[$i]->getDateDebut();
                            $dateFin = $affectCompte[$i]->getDateFin();
                            $userAffect = $affectCompte[$i]->getUserComptePartenaire();
                            
                             if ($userAffect == $data->getUserComptePartenaire() &&( $dateDebut == $data->getDateDebut()) ||  $dateFin == $data->getDateFin() )
                              {
                                throw new HttpException(403, "Cet utilisateur est dèja affecté à un compte dans même periode");
                             }
                          }
    
                }else {

                    throw new HttpException(403, "la durée d'utilisation de ce compte est terminé");
                }

            }else{
                
             throw new HttpException(403, "vous n'étes pas autorisé à ce service veiullez vous rapprocher de vos supérieur");
             
        }
                
                $this->entityManager->persist($data);
                $this->entityManager->flush();
      
    }
    public function remove($data, array $context = [])
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}