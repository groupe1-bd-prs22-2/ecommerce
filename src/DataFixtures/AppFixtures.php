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
            $product->setPrice(mt_rand(0, 10));
            $product->setQuantity(mt_rand(0,8000));
            $product->setCategory($category->setName('Shonen'));
            $product->setDescription('blablapoukie');
            $product->setPicture("Tome".$i.'.jpg');
            $manager->persist($product);
            $manager->persist($category);
        }


        $manager->flush();
    }
}
