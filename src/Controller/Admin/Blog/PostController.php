<?php

namespace App\Controller\Admin\Blog;

use App\Entity\Blog\Post;
use App\Form\Admin\Blog\PostType;
use App\Repository\Blog\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/blog/post')]
class PostController extends AbstractController
{
    #[Route('/', name: 'admin_blog_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $posts = $paginator->paginate(
            $postRepository->findBy([], ['id' => 'desc']),
            $request->query->getInt('page', 1)
        );

        return $this->render('admin/blog/post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/new', name: 'admin_blog_post_new', methods: ['GET','POST'])]
    public function new(Request $request): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', 'Post added.');

            return $this->redirectToRoute('admin_blog_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/blog/post/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_blog_post_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('admin/blog/post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_blog_post_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Post $post): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Changes saved.');

            return $this->redirectToRoute('admin_blog_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/blog/post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_blog_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($post);
            $entityManager->flush();
            $this->addFlash('success', 'Post deleted.');
        } else {
            $this->addFlash('warning', 'Something went wrong! Try again.');
        }

        return $this->redirectToRoute('admin_blog_post_index', [], Response::HTTP_SEE_OTHER);
    }
}
