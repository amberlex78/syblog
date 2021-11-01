<?php

namespace App\Controller\Front;

use App\Entity\Blog\Category;
use App\Repository\Blog\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    #[Route('/category/{slug}', name: 'front_blog_category', methods: ['GET'])]
    public function category(Category $category, PostRepository $postRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $posts = $paginator->paginate(
            $postRepository->findBy(['category' => $category->getId(), 'isActive' => true], ['id' => 'desc']),
            $request->query->getInt('page', 1)
        );

        return $this->render('front/blog/category.html.twig', [
            'category' => $category,
            'posts' => $posts,
        ]);
    }

    #[Route('/post/{slug}', name: 'front_blog_post', methods: ['GET'])]
    public function post(string $slug, PostRepository $postRepository): Response
    {
        $post = $postRepository->findOneBy(['slug' => $slug, 'isActive' => true]);
        if (!$post) {
            throw new NotFoundHttpException();
        }

        return $this->render('front/blog/post.html.twig', [
            'categorySlug' => $post->getCategory()->getSlug(),
            'post' => $post,
        ]);
    }
}
