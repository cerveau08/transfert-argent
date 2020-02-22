<?php

namespace App\Controller;

use App\Entity\Partenaire;
use App\Entity\User;
use App\Repository\PartenaireRepository;
use App\Repository\ProfilRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PartenaireController extends AbstractController
{
    private $tokenStorage;
    public function __construct(TokenStorageInterface $tokenStorage )
    {
        $this->tokenStorage = $tokenStorage;
    }
    /**
     * @Route("/api/bloquer/{id}", name="bloquer_partenaire", methods={"PUT"})
     */
   public function bloquer (Request $request, EntityManagerInterface $manager,PartenaireRepository $partenaireRepository, UserRepository $userRepository, ProfilRepository $roleRepository,$id)
   {
        $val = json_decode($request->getContent());

        $partenaire = $this->getDoctrine()->getRepository(Partenaire::class);
        $partenaireid = $partenaire->find($val->id);
        
        $userp = $this->entityManager->getRepository(User::class);
        $userpartenaire = $userp->findByPartnaire($val->id);
        
        foreach ($userpartenaire as $bloque){
            
                $bloque->setIsActive($val->isActive);
                $manager->persist($bloque);
                $manager->flush();
            
        
        }
        
            return new JsonResponse("ok");
   }
}
