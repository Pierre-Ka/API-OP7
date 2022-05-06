<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProductFixtures extends Fixture
{
    public const NUMBER_OF_PRODUCT = 50;

    const BRAND = [
        'Samsung',
        'Apple',
        'Huawei',
        'Xiaomi',
        'Honor',
        'Oppo',
        'Sony',
        'Nokia',
    ];
    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < self::NUMBER_OF_PRODUCT; $i++) {
            $brands = [];
            foreach (self::BRAND as $key => $BRAND) {
                $brands[] = $BRAND;
            }
            $product = new Product();
            shuffle($brands);
            $product->setBrand($brands[0]);
            $product->setModel($faker->bothify('??-##'));
            $product->setPrice($faker->randomFloat(2, 45, 300));
            $product->setReference($faker->uuid());
            $product->setDescription($faker->paragraphs(2, true));
            $product->setCreatedAt($faker->dateTimeThisDecade());
            $manager->persist($product);
        }
        $manager->flush();
    }
}