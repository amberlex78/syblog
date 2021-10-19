<?php

namespace App\DataFixtures;

use App\Entity\Blog\Category;
use App\Entity\Blog\Post;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class BlogFixtures extends BaseFixture implements DependentFixtureInterface
{
    private SluggerInterface $slugger;

    public function __construct(EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        parent::__construct($entityManager);
        $this->slugger = $slugger;
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $category = new Category();
        $category->setName('Symfony');
        $category->setSlug('symfony');
        $category->setDescription($this->faker->realText(500));
        $this->entityManager->persist($category);
        $this->addReference('category_' . 1, $category);

        $category = new Category();
        $category->setName('Laravel');
        $category->setSlug('laravel');
        $category->setDescription($this->faker->realText(500));
        $this->entityManager->persist($category);
        $this->addReference('category_' . 2, $category);

        // Fake data
        for ($i = 0; $i < 20; $i++) {
            $title = $this->faker->sentence(8);
            $post = new Post();

            /** @var User $user For example only `admin@example.com` or `sadmin@example.com` */
            $user = $this->getReference('user_'. $this->faker->numberBetween(2, 3));
            $post->setUser($user);

            /** @var Category $category */
            $category = $this->getReference('category_'. $this->faker->numberBetween(1, 2));
            $post->setCategory($category);

            $post->setTitle($title);
            $post->setSlug($this->slugger->slug($title)->lower());
            $post->setPreview($this->faker->realText(500));
            $post->setContent($this->faker->realText(800));
            $post->setIsDraft($this->faker->numberBetween(0, 1));
            $this->entityManager->persist($post);
        }

        $manager->flush();
    }
}
