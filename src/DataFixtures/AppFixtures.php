<?php

namespace App\DataFixtures;

use App\Entity\StaticStorage\UserRolesStorage;
use App\Factory\Blog\CategoryFactory;
use App\Factory\Blog\PostFactory;
use App\Factory\PageFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        // Pages
        PageFactory::createOne(['title' => 'About', 'slug' => 'about', 'isActive' => true]);

        // Random pages
        PageFactory::createMany(4);

        // Users
        foreach ([
            ['email' => 'user@example.com', 'roles' => [UserRolesStorage::ROLE_USER], 'isVerified' => true],
            ['email' => 'admin@example.com', 'roles' => [UserRolesStorage::ROLE_ADMIN], 'isVerified' => true],
            ['email' => 'sadmin@example.com', 'roles' => [UserRolesStorage::ROLE_SUPER_ADMIN], 'isVerified' => true],
        ] as $user ) {
            UserFactory::createOne($user);
        }

        // Random users
        UserFactory::createMany(7);

        // Blog categories
        $names = ['Linux', 'Symfony', 'Laravel', 'Slim', 'Python', 'Django',];
        foreach ($names as $name) {
            CategoryFactory::createOne([
                'name' => $name,
                'slug' => $this->slugger->slug($name)->lower(),
            ]);
        }

        // Blog posts
        PostFactory::createMany(30, function () {
            return [
                'user' => UserFactory::random(),
                'category' => CategoryFactory::random(),
            ];
        });

        $manager->flush();
    }
}
