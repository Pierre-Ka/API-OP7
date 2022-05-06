<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProductFixtures extends Fixture
{
    public const NUMBER_OF_PRODUCT = 100;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i <= self::NUMBER_OF_PRODUCT; $i++) {
            $product = new Product();
            $product->setBrand($faker->company());
            $product->setModel($faker->bothify('??-###'));
            $product->setPrice($faker->randomFloat(2, 45, 300));
            $product->setReference($faker->uuid());
            $product->setDescription($faker->paragraphs(2, true));
//          $user->setCreateDate($faker->dateTimeThisDecade());
            $manager->persist($product);
        }
        $manager->flush();
    }
}