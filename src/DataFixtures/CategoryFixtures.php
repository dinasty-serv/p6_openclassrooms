<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Util\Util;


class CategoryFixtures extends Fixture
{
    private $util;

    public function __construct(Util $util){
        $this->util = $util;
    }

    public function load(ObjectManager $manager)
    {
        $categories = [
            "Les grabs",
            "Les rotations",
            "Les flips",
            "Les rotations désaxées",
            "Les slides",
            "Les one foot tricks",
            "Old school"
        ];
        foreach ($categories as $category) {
            $categoryEntity = new Category();
            $categoryEntity->setName($category);
            $categoryEntity->setSlug($this->util->getSlug($category));
            $manager->persist($categoryEntity);
            $manager->flush();

        }
        // $product = new Product();
        //
    }

}
