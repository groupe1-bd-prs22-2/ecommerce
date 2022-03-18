<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Product;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $category = new Category();
        for ($i = 1; $i <= 25; $i++) {
            $product = new Product();
            $product->setName('One Pièce Tome : '.$i);
            $product->setPrice(mt_rand(1, 15));
            $product->setQuantity(mt_rand(0,8000));
            $product->addCategory($category->setName('OnePiece'));
            $product->setDescription('blablapoukie');
            $product->setPicture("Tome".$i.'.jpg');
            $manager->persist($product);
            $manager->persist($category);
        }


        $manager->flush();
    }
}
