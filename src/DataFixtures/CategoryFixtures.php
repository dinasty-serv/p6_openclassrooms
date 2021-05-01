<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Util\Util;
class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $util = new Util();
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
            $categoryEntity->setSlug($util->getSlug($category));
            $manager->persist($categoryEntity);
            $manager->flush();

        }
        // $product = new Product();
        //
    }
}
