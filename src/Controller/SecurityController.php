<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Algorithm\Algorithm;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SecurityController extends AbstractController
{

    /*
    public function __construct(TokenStorageInterface $tokenStorage,Algorithm $algo,UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->tokenStorage = $tokenStorage;
        $this->algo=$algo;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }
    public function __invoke(User $data)
    {
        //variable role user connecté
        $userRoles=$this->tokenStorage->getToken()->getUser()->getRoles()[0];
        //variable role user à modifier
        $usersModi=$data->getRoles()[0];
        if($this->algo->isAuthorised($userRoles,$usersModi) == true){
            $data->setPassword($this->userPasswordEncoder->encodePassword($data, $data->getPassword()));
            $data->setImage($data->getImage());
            return $data;
        }else{
            throw new HttpException("401","Access non Authorisé");
        }
    }
    */

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
