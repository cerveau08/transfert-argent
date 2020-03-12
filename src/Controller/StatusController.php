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
