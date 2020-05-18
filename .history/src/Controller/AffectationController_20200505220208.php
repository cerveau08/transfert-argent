<?php

namespace App\Controller;

use DateTime;
use App\Entity\Affectation;
use App\Repository\AffectationRepository;
use App\Repository\CompteRepository;
use App\Repository\PartenaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class AffectationController {
    public function __construct(TokenStorageInterface $tokenStorage, AffectationRepository $affectCompteRepo){

        $this->tokenStorage = $tokenStorage;
        $this->affectCompteRepo = $affectCompteRepo;
    }

  
}