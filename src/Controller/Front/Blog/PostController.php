<?php

namespace App\Controller\Front\Blog;

use App\Repository\Blog\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/post/{slug}', name: 'front_blog_post', methods: ['GET'])]
    public function post(string $slug, PostRepository $postRepository): Response
    {
        if (!$post = $postRepository->findOneActiveBySlug($slug)) {
            throw new NotFoundHttpException();
        }

        return $this->render('front/blog/post.html.twig', [
            'categorySlug' => $post->getCategory()->getSlug(),
            'post' => $post,
        ]);
    }
}
