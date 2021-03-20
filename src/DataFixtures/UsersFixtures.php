<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Util\Util;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsersFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {

        $user = new User();
        $user->setUsername('admin');
        $user->setEmail('nicodu22300@hotmail.fr');
        $user->setStatus(true);

        $password = $this->encoder->encodePassword($user, '28121995');
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();
        // $product = new Product();
        //
    }
}
