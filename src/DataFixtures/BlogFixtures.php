<?php

namespace App\DataFixtures;

use App\Entity\Blog\Category;
use App\Entity\Blog\Post;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class BlogFixtures extends BaseFixture
{
    private SluggerInterface $slugger;

    public function __construct(EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        parent::__construct($entityManager);
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $object = new Category();
        $object->setName('Symfony');
        $object->setSlug('symfony');
        $object->setDescription($this->faker->realText(500));
        $this->entityManager->persist($object);
        $this->addReference('category_' . 1, $object);

        $object = new Category();
        $object->setName('Laravel');
        $object->setSlug('laravel');
        $object->setDescription($this->faker->realText(500));
        $this->entityManager->persist($object);
        $this->addReference('category_' . 2, $object);

        // Fake data
        for ($i = 0; $i < 20; $i++) {
            $title = $this->faker->sentence(8);
            $object = new Post();
            /** @var Category $category */
            $category = $this->getReference('category_'. $this->faker->numberBetween(1, 2));
            $object->setCategory($category);
            $object->setTitle($title);
            $object->setSlug($this->slugger->slug($title)->lower());
            $object->setPreview($this->faker->realText(500));
            $object->setContent($this->faker->realText(800));
            $object->setIsDraft($this->faker->numberBetween(0, 1));
            $this->entityManager->persist($object);
        }

        $manager->flush();
    }
}
