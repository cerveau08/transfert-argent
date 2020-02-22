<?php

// src/EventListener/JWTCreatedListener.php

namespace App\EventListener;

use App\Repository\AffectationRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Config\Definition\Exception\Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class JWTCreatedListener
{

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack, AffectationRepository $aff)
    {
        $this->requestStack = $requestStack;
        $this->aff=$aff;
    }

    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        /** @var $user \AppBundle\Entity\User */
        $user = $event->getUser();
    //Bloque le compte si la metode getIsActive() est egale a false By Son Excellence WADE
        if ($user->getIsActive() == false){
            throw new  CustomUserMessageAuthenticationException('Votre compte a été bloqué');;
           
        }
        if($user->getPartenaire() != null){
            if(!$user->getPartenaire()->getUserComptePartenaire()[0]->getIsActive()){
                throw new Exception('Votre Agence est bloquée!!!');
            }
            
        }
        if($this->aff->findCompteAffectTo($user) != []){
            $aujourd = date('Y-m-d');
        $aujourd=date('Y-m-d', strtotime($aujourd));
        //echo $paymentDate; // echos today!
        //Dernier Affectations
        $affects=$this->aff->findCompteAffectTo($user)[0];
        foreach ($affects as $dateaffect){
        $debut=$dateaffect->getDateDebut();
        $fin=$dateaffect->getDateFin();
        $debut=date_format($debut,"Y-m-d");
        $fin=date_format($fin,"Y-m-d");
        if (!(($aujourd >= $debut) && ($aujourd <= $fin))){
           throw new Exception("Vous etes pas associé à aucun compte ");
        }
        }
        /*else{
            throw new Exception("Votre Compte utilsateur n'est affecté à aucun compte");
         }*/
        }

        // merge with existing event data
        $payload = array_merge(
            $event->getData(),
            [
                'password' => $user->getPassword()
            ]
        );

        $event->setData($payload);
    }
}