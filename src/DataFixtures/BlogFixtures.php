<?php

namespace App\DataFixtures;

use App\Factory\Blog\CategoryFactory;
use App\Factory\Blog\PostFactory;
use App\Factory\Blog\TagFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BlogFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        foreach (['Linux', 'Database', 'PHP', 'Python', 'Symfony', 'Laravel', 'Slim', 'Django', 'Algorithms'] as $name) {
            CategoryFactory::createOne(['name' => $name, 'isActive' => true]);
        }
        CategoryFactory::createMany(3);

        PostFactory::createMany(50, function () {
            return [
                'user' => UserFactory::random(),
                'category' => CategoryFactory::random(),
                'tags' => TagFactory::new()->many(0, 3),
            ];
        });

        $manager->flush();
    }
}
