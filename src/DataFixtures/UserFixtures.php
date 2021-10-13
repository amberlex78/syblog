<?php

namespace App\DataFixtures;

use App\Entity\StaticStorage\UserRolesStorage;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    private EntityManagerInterface $em;

    public function __construct(UserPasswordHasherInterface $hasher, EntityManagerInterface $entityManager)
    {
        $this->hasher = $hasher;
        $this->em = $entityManager;
    }

    public function load(ObjectManager $manager): void
    {
        $usersData = [
            [
                'email' => 'user@example.com',
                'role' => [UserRolesStorage::ROLE_USER],
                'password' => 'password',
            ],
            [
                'email' => 'admin@example.com',
                'role' => [UserRolesStorage::ROLE_ADMIN],
                'password' => 'password',
            ],
            [
                'email' => 'sadmin@example.com',
                'role' => [UserRolesStorage::ROLE_SUPER_ADMIN],
                'password' => 'password',
            ],
        ];

        foreach ($usersData as $user) {
            $newUser = new User();

            $newUser->setEmail($user['email']);
            $newUser->setPassword($this->hasher->hashPassword($newUser, $user['password']));
            $newUser->setRoles($user['role']);

            $this->em->persist($newUser);
        }

        $manager->flush();
    }
}
