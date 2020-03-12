<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class QuotaController extends AbstractController
{
    /**
     * @Route("/quota", name="quota")
     */
    public function index()
    {
        return $this->render('quota/index.html.twig', [
            'controller_name' => 'QuotaController',
        ]);
    }
}
