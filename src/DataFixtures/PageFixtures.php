<?php

namespace App\DataFixtures;

use App\Entity\Page;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

class PageFixtures extends Fixture
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function load(ObjectManager $manager): void
    {
        $data = [
            [
                'title' => 'About',
                'slug' => 'about',
                'is_active' => true,
            ],
            [
                'title' => 'Payment',
                'slug' => 'payment',
            ],
            [
                'title' => 'Delivery',
                'slug' => 'delivery',
            ],
        ];

        foreach ($data as $item) {
            $newUser = new Page();

            $newUser->setTitle($item['title']);
            $newUser->setSlug($item['slug']);
            $newUser->setIsActive($item['is_active'] ?? false);

            $this->em->persist($newUser);
        }

        $manager->flush();
    }
}
