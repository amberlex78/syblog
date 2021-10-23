<?php

namespace App\DataFixtures;

use App\Factory\PageFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PageFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        PageFactory::createOne([
            'title' => 'About',
            'slug' => 'about',
            'isActive' => true,
            'seoTitle' => 'Seo Title About',
            'seoKeywords' => 'Seo Keywords About',
            'seoDescription' => 'Seo Description About ',
        ]);

        PageFactory::createMany(4);

        $manager->flush();
    }
}
