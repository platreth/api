<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $em)
    {
        $faker = Faker\Factory::create('fr_FR');

        // on créé 10 produits random avec faker
        for ($i = 0; $i < 10; $i++) {

            $product = new Product();
            $product->setName($faker->word);
            $product->setDescription($faker->paragraph);
            $product->setPrice($faker->numberBetween(10, 100));

            $em->persist($product);


        }
        $em->flush();
    }
}
