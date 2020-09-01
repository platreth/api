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

    // on créé 10 personnes random avec faker
    for ($i = 0; $i < 10; $i++) {

        $client = new Client();
        $client->setName($faker->name);
        $client->setUsername($faker->userName);
        $client->setPassword($faker->password);
        $em->persist($client);

        $user = new User();
        $user->setClient($client);
        $user->setLastname($faker->lastName);
        $user->setFirstname($faker->firstName);
        $em->persist($user);

    }
    // COMPTE DE TEST
    $client_test = new Client();
    $client_test->setName("user");
    $client_test->setUsername("user");
    $client_test->setPassword(password_hash("pass", 1));
    $em->persist($client_test);

    $em->flush();
}
}
