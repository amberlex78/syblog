<?php

namespace App\Controller\Admin\Blog;

use App\Entity\Blog\Post;
use App\Entity\Blog\Tag;
use App\Form\Admin\Blog\PostType;
use App\Form\Admin\Blog\TagType;
use App\Service\Blog\PostService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/blog/post', name: 'admin_blog_post_')]
class PostController extends AbstractController
{
    public function __construct(
        private PostService $postService,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $posts = $this->postService->findAllFiltered(
            $request->query->getInt('category'),
            $request->query->getInt('tag'),
            $request->query->getInt('user'),
        );

        $posts = $paginator->paginate($posts, $request->query->getInt('page', 1));

        return $this->render('admin/blog/post/index.html.twig', compact('posts'));
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $formTag = $this->createForm(TagType::class, new Tag());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->postService->handleCreate($form, $post);
            $this->addFlash('success', 'Post created!');

            return $this->redirectToRoute('admin_blog_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/blog/post/new.html.twig', compact('post', 'form', 'formTag'));
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('admin/blog/post/show.html.twig', compact('post'));
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Post $post, Request $request): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $formTag = $this->createForm(TagType::class, new Tag());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->postService->handleEdit($form, $post);
            $this->addFlash('success', 'Changes saved!');

            return $this->redirectToRoute('admin_blog_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/blog/post/edit.html.twig', compact('post', 'form', 'formTag'));
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Post $post, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            $this->postService->handleDelete($post);
            $this->addFlash('success', 'Post deleted!');
        } else {
            $this->addFlash('warning', 'Something went wrong! Try again.');
        }

        return $this->redirectToRoute('admin_blog_post_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/image/{id}', name: 'delete_image', methods: ['DELETE'])]
    public function deleteImage(Post $post, Request $request): JsonResponse
    {
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            $this->postService->handleDeleteImage($post);

            return $this->json(['success' => true]);
        } else {
            return $this->json(['error' => 'Bad Request!'], 400);
        }
    }

    #[Route('/tag/new', name: 'tag_new', methods: ['POST'])]
    public function tagNew(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tag);
            $em->flush();
            $data = [
                'status' => true,
                'message' => 'Tag added!',
                'tag' => [
                    'id' => $tag->getId(),
                    'text' => $tag->getName(),
                ],
            ];
        } else {
            $data = [
                'status' => false,
                'errors' => $form->getErrors(),
                'message' => 'Something went wrong! Try again.',
            ];
        }

        return $this->json($data);
    }
}
