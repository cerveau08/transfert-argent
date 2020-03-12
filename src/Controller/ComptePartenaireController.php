<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ComptePartenaireController extends AbstractController
{
    /**
     * @Route("/compte/partenaire", name="compte_partenaire")
     */
    public function index()
    {
        return $this->render('compte_partenaire/index.html.twig', [
            'controller_name' => 'ComptePartenaireController',
        ]);
    }
}
