<?php

namespace App\DataFixtures;

use App\Entity\Page;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class PageFixtures extends BaseFixture
{
    private SluggerInterface $slugger;

    public function __construct(EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        parent::__construct($entityManager);
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $object = new Page();
        $object->setTitle('About');
        $object->setSlug('about');
        $object->setContent($this->faker->realText(500));
        $object->setIsActive(true);

        $this->entityManager->persist($object);

        // Fake data
        for ($i = 0; $i < 5; $i++) {
            $title = $this->faker->sentence(2);

            $object = new Page();
            $object->setTitle($title);
            $object->setSlug($this->slugger->slug($title));
            $object->setContent($this->faker->realText(500));
            $object->setIsActive($this->faker->numberBetween(0, 1));

            $this->entityManager->persist($object);
        }

        $manager->flush();
    }
}
