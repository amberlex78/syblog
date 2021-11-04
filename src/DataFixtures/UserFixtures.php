<?php

namespace App\DataFixtures;

use App\Entity\StaticStorage\UserRolesStorage;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ([
            ['email' => 'user@example.com', 'roles' => [UserRolesStorage::ROLE_USER], 'isVerified' => true],
            ['email' => 'admin@example.com', 'roles' => [UserRolesStorage::ROLE_ADMIN], 'isVerified' => true],
            ['email' => 'sadmin@example.com', 'roles' => [UserRolesStorage::ROLE_SUPER_ADMIN], 'isVerified' => true],
        ] as $user) {
            UserFactory::createOne($user);
        }

        UserFactory::createMany(2);

        $manager->flush();
    }
}
