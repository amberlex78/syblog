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
            1 => ['email' => 'user@example.com', 'role' => [UserRolesStorage::ROLE_USER]],
            2 => ['email' => 'admin@example.com', 'role' => [UserRolesStorage::ROLE_ADMIN]],
            3 => ['email' => 'sadmin@example.com', 'role' => [UserRolesStorage::ROLE_SUPER_ADMIN]],
        ];

        foreach ($users as $id => $user) {
            $newUser = new User();
            $newUser->setEmail($user['email']);
            $newUser->setRoles($user['role']);
            $newUser->setIsVerified(1);
            $newUser->setFirstName($this->faker->firstName());
            $newUser->setLastName($this->faker->lastName());
            $newUser->setPassword($this->hasher->hashPassword($newUser, 'password'));
            $this->entityManager->persist($newUser);
            $this->addReference('user_' . $id, $newUser);
        }

        $manager->flush();
    }
}
