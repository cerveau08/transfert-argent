<?php

namespace App\Controller;

use App\Entity\Partenaire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

    /** 
    * @Route("/api")
    */
class NineaController extends AbstractController
{
    private $tokenStorage;
    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/ninea", name="creation_compte_PartenaireExistent", methods={"GET"})
     */
    public function rechercheParNinea(Request $request)
    {
        $values = json_decode($request->getContent());
        if(isset($values->ninea))
        {
          /* $userconnect = $this->tokenStorage->getToken()->getUser();
           if($userconnect ===  'ROLE_ADMIN_SYSTEM' || $userconnect ===  'ROLE_ADMIN')
           {
               */
            $ninea = new Partenaire();
            $ninea->setNinea($values->ninea);
           // dd($values);
            $repositori = $this->entityManager->getRepository(Partenaire::class);
            $ninea = $repositori->findOneByNinea($values->ninea);
            
           // dd($ninea);
              if ($ninea) 
              {
                
                return new JsonResponse($ninea, 201);
              }
              else
              {
                $ninea = [
                    'status' => 401,
                    'message' => 'Désolé ce ninea n\'existe pas !!!'
                    ];         
              }
          // }
        }
    }
}
