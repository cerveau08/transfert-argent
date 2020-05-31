<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class StatusController extends AbstractController
{
    /**
     * @Route("/api/users/status/{id}", name="status", methods={"GET"})
     */
    public function status($id, UserRepository $repo)
    {
    	$repo = $this->getDoctrine()->getRepository(User::class);
        $compte = $repo->find($id);
        //dd($compte);
        $status=''; 
       if( $compte->getIsActive() === true)
       {
       	$status='desactive';
       	 $compte->setIsActive(false);
       }
       else
       {
       	$status='active';
       	$compte->setIsActive(true);
       }
       $manager = $this->getDoctrine()->getManager();
       $manager->persist($compte);
       $manager->flush(); 
       $data = [
          'status'=>200,
          'message'=>$compte->getNumeroCompte().' est '.$status
       ];
       return $this->json($data, 200);
    }

    /**
     * @Route("/api/comptes/status/{id}", name="statusCompte", methods={"GET"})
     */
    public function statusCompte($id, UserRepository $repo)
    {
      $repo = $this->getDoctrine()->getRepository(Compte::class);
        $user = $repo->find($id);
        //dd($user);
        $status=''; 
       if( $user->getIsActive() === true)
       {
        $status='desactive';
         $user->setIsActive(false);
       }
       else
       {
        $status='active';
        $user->setIsActive(true);
       }
       $manager = $this->getDoctrine()->getManager();
       $manager->persist($user);
       $manager->flush(); 
       $data = [
          'status'=>200,
          'message'=>$user->getUsername().' est '.$status
       ];
       return $this->json($data, 200);
    }
}
