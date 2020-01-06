<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Profil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api")
 */
class SecurityController extends AbstractController
{
   
     /**
     * @Route("/users", name="register", methods={"POST"})
     */
  /*  public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $values = json_decode($request->getContent());
        if(isset($values->username,$values->password)) {
            $user = new User();
            $user->setUsername($values->username);
            $user->setPassword($passwordEncoder->encodePassword($user, $values->password));
             // recuperer id profil
            $repos = $this->getDoctrine()->getRepository(Profil::class);
            $profils = $repos->find($values->profil);
            $user->setProfil($profils);
            $role = [];
            if ($profils->getLibelle() == "admin") {
                $role = (["ROLE_ADMIN"]);
            } elseif ($profils->getLibelle() == "caissier") {
                $role = (["ROLE_CAISSIER"]);
            } elseif ($profils->getLibelle() == "supadmin") {
                $role = (["ROLE_ADMIN_SYSTEM"]);
            }
            $user->setRoles($role);
            /* if(!isset($values->roles))
                {
                    $user->setRoles(['ROLE_CAISSIER']);
                }
                else
                {
                    $user->SetRoles([$values->roles]);
                }*/
         /*   $user->setLogin($values->login);
            $user->setEmail($values->email);
           
            $errors = $validator->validate($user);
            if(count($errors)) {
                $errors = $serializer->serialize($errors, 'json');
                return new Response($errors, 500, [
                    'Content-Type' => 'application/json'
                ]);
            }
            $entityManager->persist($user);
            $entityManager->flush();

            $data = [
                'status' => 201,
                'message' => 'L\'utilisateur a été créé'
            ];

            return new JsonResponse($data, 201);
        }
        $data = [
            'status' => 500,
            'message' => 'Vous devez renseigner les clés username et password'
        ];
        return new JsonResponse($data, 500);
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
