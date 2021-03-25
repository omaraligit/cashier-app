<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    /**
     * UserFixtures constructor.
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }


    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail("user@domain.com");
        $password = $this->encoder->encodePassword($user, 'SuperSecret');
        $user->setPassword($password);
        $user->setRoles(["ROLE_ADMIN","ROLE_USER"]);
        $manager->persist($user);
        $manager->flush();
    }
}
