<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Faker\Generator;

abstract class BaseFixture extends Fixture
{
    protected EntityManagerInterface $entityManager;
    protected Generator $faker;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->faker = Factory::create();
    }
}
