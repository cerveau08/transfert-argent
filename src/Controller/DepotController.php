<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DepotController extends AbstractController
{
    /**
     * @Route("/depot", name="depot")
     */
    public function index()
    {
        return $this->render('depot/index.html.twig', [
            'controller_name' => 'DepotController',
        ]);
    }
}
