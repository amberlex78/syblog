<?php

namespace App\DataFixtures;

use App\Factory\Blog\CategoryFactory;
use App\Factory\Blog\PostFactory;
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
        foreach ([
            'linux' => 'Linux',
            'symfony' => 'Symfony',
            'laravel' => 'Laravel',
            'slim' => 'Slim',
            'python' => 'Python',
            'django' => 'Django',
        ] as $slug => $name) {
            CategoryFactory::createOne(['name' => $name, 'slug' => $slug, 'isActive' => true]);
        }

        CategoryFactory::createMany(4);

        PostFactory::createMany(30, function () {
            return [
                'user' => UserFactory::random(),
                'category' => CategoryFactory::random(),
            ];
        });

        $manager->flush();
    }
}
