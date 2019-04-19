<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        // Creating random admin user

        $user = new User();

        $user->setEmail("admin@gmail.com");
        $user->setPassword($this->passwordEncoder->encodePassword($user,"admin"));
        $user->setNom("ADMIN");
        $user->setPrenom("Admin");
        $user->setPhone("0548178727");
        $user->setRoles(["ROLE_ADMIN"]);

        $manager->persist($user);

        $manager->flush();
    }
}
