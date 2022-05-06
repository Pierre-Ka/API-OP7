<?php

namespace App\DataFixtures;

use App\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ClientFixtures extends Fixture
{
    public const NUMBER_OF_CLIENT = 10;
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i <= self::NUMBER_OF_CLIENT; $i++) {
            $client = new Client();
            $client->setEmail($faker->email());
            $client->setName($faker->domainName());
            $password = $this->hasher->hashPassword($client, 'secret');
            $client->setPassword($password);
//          $user->setCreateDate($faker->dateTimeThisDecade());
            $manager->persist($client);
            $this->addReference('client_'.$i, $client);
        }
        $manager->flush();
    }
}