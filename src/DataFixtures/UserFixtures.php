<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Profil;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    { 
        $profil = new Profil();
        $profil->setLibelle('ROLE_ADMIN_SYSTEM');
        $manager->persist($profil);
        $manager->flush();
        
        $user = new User();
        $user->setUsername('cerv');
        $user->setLogin('admin');
        $user->setProfil($profil);
        $password=$this->encoder->encodePassword($user,'Admin');
        $user->setPassword($password);
        $user->setRoles(array('ROLE_ADMIN_SYSTEM'));
        $user->setIsActive(true);
        $user->setEmail('cerv@gmail.com');

        $manager->persist($user);
        $manager->flush();
    }
}