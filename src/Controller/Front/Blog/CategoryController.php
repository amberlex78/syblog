<?php

namespace App\Controller\Front\Blog;

use App\Repository\Blog\CategoryRepository;
use App\Repository\Blog\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    public function __construct(
        protected CategoryRepository $categoryRepository,
        protected PaginatorInterface $paginator,
        protected PostRepository $postRepository,
    ) {
    }

    #[Route('/category/{slug}', name: 'front_blog_category', methods: ['GET'])]
    public function category(string $slug, Request $request): Response
    {
        if (!$category = $this->categoryRepository->findOneActiveBySlug($slug)) {
            throw new NotFoundHttpException();
        }

        $posts = $this->paginator->paginate(
            $this->postRepository->findAllActiveInCategory($category->getId()),
            $request->query->getInt('page', 1)
        );

        return $this->render('front/blog/category.html.twig', [
            'category' => $category,
            'posts' => $posts,
        ]);
    }
}
