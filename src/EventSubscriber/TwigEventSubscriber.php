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
    public function __construct(
        private Environment $twig,
        private CategoryRepository $categoryRepository,
        private PageRepository $pageRepository,
        private PostRepository $postRepository,
    ) {
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
        $this->twig->addGlobal('menuPages', $this->pageRepository->findAllActiveSlugTitle());
        $this->twig->addGlobal('recentPosts', $this->postRepository->findRecentActiveSlugTitle());
        $this->twig->addGlobal('menuCategories', $this->categoryRepository->findAllActiveSlugName());
    }
}
