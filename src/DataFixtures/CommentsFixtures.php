<?php


namespace App\DataFixtures;


use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;


class CommentsFixtures extends Fixture implements DependentFixtureInterface
{


    public function load(ObjectManager $manager)
    {
        $tricks = $manager->getRepository(Trick::class)->findAll();
        foreach ($tricks as $trick){
        $limitPerTricks = rand ( 1 , 50 );

        for ($i = 1; $i < $limitPerTricks; $i++) {

            $userId =  rand ( 1 , 3 );
            $user = $manager->getRepository(User::class)->find($userId);

            $comment = new Comment();
            $comment->setContent('test commentaire '.$i);
            $comment->setUser($user);
            $comment->setTrick($trick);
            $manager->persist($comment);
            $manager->flush();
             }
        }
    }

    public function getDependencies(): array
    {
        return [
            TricksFixtures::class,
            UsersFixtures::class,
        ];
    }
}