<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class ClientFixtures extends Fixture
{
public function load(ObjectManager $em)
{
    $faker = Faker\Factory::create('fr_FR');

    // on créé 10 personnes
    for ($i = 0; $i < 10; $i++) {

        $client = new Client();
        $client->setName($faker->name);
        $em->persist($client);

        $user = new User();
        $user->setClient($client);
        $user->setLastname($faker->lastName);
        $user->setFirstname($faker->firstName);
        $em->persist($user);

    }
    $em->flush();
}
}
