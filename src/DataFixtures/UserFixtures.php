<?php

namespace App\DataFixtures;

use App\Entity\Profil;
use App\Entity\User;
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
        $roleadminsysteme=new Profil();
        $roleadminsysteme->setLibelle('ROLE_ADMIN_SYSTEM');
        $manager->persist($roleadminsysteme);
        
        $roleadmin=new Profil();
        $roleadmin->setLibelle('ROLE_ADMIN');
        $manager->persist($roleadmin);
        
        $rolecaissier=new Profil();
        $rolecaissier->setLibelle('ROLE_CAISSIER');
        $manager->persist($rolecaissier);
        
        $rolepartenaire=new Profil();
        $rolepartenaire->setLibelle('ROLE_PARTENAIRE');
        $manager->persist($rolepartenaire);


        $adminsysteme = new User();
        $password = $this->encoder->encodePassword($adminsysteme, 'admin');
        $adminsysteme->setUsername('malick')
             ->setPassword($password)
             ->setEmail('malickcoly342@gmail.com')
             ->setIsActive(true)
             ->setProfil($roleadminsysteme);
        $manager->persist($adminsysteme);

        $manager->flush();
    
}

}
