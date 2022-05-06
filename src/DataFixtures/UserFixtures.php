<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public const NUMBER_OF_USER = 100;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i <= self::NUMBER_OF_USER; $i++) {

            $clientKey = rand(0, (ClientFixtures::NUMBER_OF_CLIENT - 1));
            /** @var Client $client */
            $client = $this->getReference('client_' . $clientKey);

            $user = new User();
            $user->setClient($client);
            $user->setEmail($faker->email());
            $user->setFirstName($faker->firstName());
            $user->setLastName($faker->lastName());
//          $user->setCreateDate($faker->dateTimeThisDecade());
            $manager->persist($user);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ClientFixtures::class,
        ];
    }
}