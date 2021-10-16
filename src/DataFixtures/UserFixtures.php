<?php

namespace App\DataFixtures;

use App\Entity\StaticStorage\UserRolesStorage;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends BaseFixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher)
    {
        parent::__construct($entityManager);
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $users = [
            ['email' => 'user@example.com', 'role' => [UserRolesStorage::ROLE_USER]],
            ['email' => 'admin@example.com', 'role' => [UserRolesStorage::ROLE_ADMIN]],
            ['email' => 'sadmin@example.com', 'role' => [UserRolesStorage::ROLE_SUPER_ADMIN]],
        ];

        foreach ($users as $user) {
            $object = new User();
            $object->setEmail($user['email']);
            $object->setRoles($user['role']);
            $object->setIsVerified(1);
            $object->setFirstName($this->faker->firstName());
            $object->setLastName($this->faker->lastName());
            $object->setPassword($this->hasher->hashPassword($object,'password'));

            $this->entityManager->persist($object);
        }

        $manager->flush();
    }
}
