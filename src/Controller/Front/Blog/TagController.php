<?php

namespace App\Controller\Front\Blog;

use App\Repository\Blog\PostRepository;
use App\Repository\Blog\TagRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class TagController extends AbstractController
{
    public function __construct(
        protected PaginatorInterface $paginator,
        protected PostRepository $postRepository,
        protected TagRepository $tagRepository,
    ) {
    }

    #[Route('/tag/{slug}', name: 'front_blog_tag', methods: ['GET'])]
    public function post(string $slug, Request $request): Response
    {
        if (!$tag = $this->tagRepository->findOneBySlug($slug)) {
            throw new NotFoundHttpException();
        }

        $posts = $this->paginator->paginate(
            $this->postRepository->findAllActiveByTag($slug),
            $request->query->getInt('page', 1)
        );

        return $this->render('front/blog/tag.html.twig', compact('tag', 'posts'));
    }
}
