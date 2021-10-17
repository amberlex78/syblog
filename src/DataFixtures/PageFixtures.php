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
        // For example, pages for Header (first variant: via object)
        $page = new Page();
        $page->setTitle('Header Pages');
        $page->setSlug('header-pages');
        $this->entityManager->persist($page);

        $subPage = new Page();
        $subPage->setParent($page);
        $subPage->setTitle('About');
        $subPage->setSlug('about');
        $subPage->setContent($this->faker->realText(500));;
        $subPage->setIsActive(true);
        $this->entityManager->persist($subPage);

        $subPage = new Page();
        $subPage->setParent($page);
        $subPage->setTitle('Delivery');
        $subPage->setSlug('delivery');
        $subPage->setContent($this->faker->realText(500));;
        $subPage->setIsActive(true);
        $this->entityManager->persist($subPage);

        // For example, pages for footer (second variant: via reference)
        $page = new Page();
        $page->setTitle('Footer Pages');
        $page->setSlug('footer-pages');
        $this->entityManager->persist($page);
        $this->addReference('page_' . 4, $page); // Save page in reference

        $subPage = new Page();
        /** @var Page $parent */
        $parent = $this->getReference('page_'. 4); // Get page in reference
        $subPage->setParent($parent);
        $subPage->setTitle('Privacy Policy');
        $subPage->setSlug('privacy-policy');
        $subPage->setContent($this->faker->realText(500));;
        $subPage->setIsActive(true);
        $this->entityManager->persist($subPage);

        $subPage = new Page();
        /** @var Page $parent */
        $parent = $this->getReference('page_'. 4); // Get page in reference
        $subPage->setParent($parent);
        $subPage->setTitle('Terms and Conditions');
        $subPage->setSlug('terms-and-conditions');
        $subPage->setContent($this->faker->realText(500));;
        $subPage->setIsActive(true);
        $this->entityManager->persist($subPage);

        // 6 pages were added

        // -----------------------
        // Add 3 pages (fake data)
        for ($i = 7; $i < 10; $i++) {
            $title = $this->faker->sentence(2);
            $page = new Page();
            $page->setTitle($title);
            $page->setSlug($this->slugger->slug($title));
            $page->setContent($this->faker->realText(500));
            $page->setIsActive($this->faker->numberBetween(0, 1));
            $this->entityManager->persist($page);
            $this->addReference('page_' . $i, $page); // Save page in reference
        }

        // Add sub-pages for 3 pages (fake data)
        $num = $this->faker->numberBetween(2, 5);
        for ($j = 0; $j < 15; $j++) {
            $title = $this->faker->sentence(2);
            $object = new Page();
            /** @var Page $parent */
            $parent = $this->getReference('page_'. $this->faker->numberBetween(7, 9));
            $object->setParent($parent);
            $object->setTitle($title);
            $object->setSlug($this->slugger->slug($title));
            $object->setContent($this->faker->realText(500));
            $object->setIsActive($this->faker->numberBetween(0, 1));
            $this->entityManager->persist($object);
        }

        $manager->flush();
    }
}
