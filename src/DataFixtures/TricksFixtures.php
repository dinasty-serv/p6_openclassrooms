<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Util\Util;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;


class TricksFixtures extends Fixture implements DependentFixtureInterface
{
    private $util;

    public function __construct(Util $util){
        $this->util = $util;
    }

    public function load(ObjectManager $manager)
    {
        $titles = [
            "Les grabs",
            "Les rotations",
            "Les flips",
            "Les rotations désaxées",
            "Les slides",
            "Les one foot tricks",
            "Old school"
        ];
        $limit = 10;



      for ($i = 1; $i <= $limit; $i++){
            foreach ($titles as $title){
                $trick = new Trick();
                $image = new Image();
                $image->setName($title.'-'.$i);
                $image->setPath("upload/tricks/grabs.jpeg");

                $trick->setName($title.'-'.$i);
                $trick->setSlug($this->util->getSlug($trick->getName()));
                $trick->setImgDefault($image);
                $trick->setContent(" is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley");
                $userId =  rand ( 1 , 3 );

                $user = $manager->getRepository(User::class)->findOneBy(['id' => $userId]);
                $trick->setUser($user);
                $categorie = $manager->getRepository(Category::class)->findOneBy(['name' => $title]);
                $trick->setCategory($categorie);
                $manager->persist($trick);
                $manager->flush();
            }
        }
    }
    public function getDependencies(): array
    {
        return [
            UsersFixtures::class,
            CategoryFixtures::class,
        ];
    }
}
