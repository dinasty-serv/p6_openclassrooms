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

        $user2 = new User();
        $user2->setUsername('user1');
        $user2->setEmail('test@email.com');
        $user2->setStatus(false);

        $password = $this->encoder->encodePassword($user, '0000');
        $user2->setPassword($password);

        $manager->persist($user2);
        $manager->flush();
        //
    }
}
