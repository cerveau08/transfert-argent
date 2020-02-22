<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\ProfilRepository;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SecurityController extends AbstractController
{
    public function __construct(ProfilRepository $repo,TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
        $this->repo=$repo;
    }
    public function __invoke(User $data)
    {
        
        $userConect=$this->tokenStorage->getToken()->getUser();
        $userPartenaire=$userConect->getPartenaire();
        ///variable role user connecté
        $userRoles=$userConect->getRoles()[0];
        if($userRoles == "ROLE_PARTENAIRE" || $userRoles == "ROLE_ADMIN_PARTENAIRE")
        {
            $data->setPartenaire($userPartenaire);
        }
        //variable role user à modifier
        /*$usersModi=$data->getRoles()[0];
        if($this->algo->isAuthorised($userRoles,$usersModi) == true){
            $data->setPassword($this->userPasswordEncoder->encodePassword($data, $data->getPassword()));
            return $data;
        }else{
            throw new HttpException("401","Access non Authorisé");
        }
        */
    }

    /**
     * @Route("/login_check", name="login", methods={"POST"})
     */
    public function login()
    {
        $user = $this->getUser();
        return $this->json([
            'username' => $user->getUsername(),
            'roles' => $user->getRoles()
        ]);
    }
}
