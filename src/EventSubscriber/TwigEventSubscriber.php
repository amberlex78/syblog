<?php

namespace App\EventSubscriber;

use App\Repository\Blog\CategoryRepository;
use App\Repository\Blog\PostRepository;
use App\Repository\PageRepository;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

class TwigEventSubscriber implements EventSubscriberInterface
{
    private Environment $twig;
    private CategoryRepository $categoryRepository;
    private PageRepository $pageRepository;
    private PostRepository $postRepository;

    public function __construct(
        Environment        $twig,
        CategoryRepository $categoryRepository,
        PageRepository     $pageRepository,
        PostRepository     $postRepository,
    )
    {
        $this->twig = $twig;
        $this->categoryRepository = $categoryRepository;
        $this->pageRepository = $pageRepository;
        $this->postRepository = $postRepository;
    }

    #[ArrayShape(['kernel.controller' => 'string'])]
    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.controller' => 'onControllerEvent',
        ];
    }

    public function onControllerEvent(ControllerEvent $event)
    {
        $this->twig->addGlobal('menuPages', $this->pageRepository->findBy(['isActive' => true]));
        $this->twig->addGlobal('recentPosts', $this->postRepository->findBy(['isActive' => true], ['id' => 'desc'], 5));
        $this->twig->addGlobal('menuCategories', $this->categoryRepository->findAll());
    }
}
