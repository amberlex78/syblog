<?php

namespace App\Controller\Admin\Blog;

use App\Entity\Blog\Post;
use App\Entity\Blog\Tag;
use App\Form\Admin\Blog\PostType;
use App\Form\Admin\Blog\TagType;
use App\Repository\Blog\PostRepository;
use App\Service\Uploader\BlogUploader;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
            $postRepository->findAllOrderedByNewest(),
            $request->query->getInt('page', 1)
        );

        return $this->render('admin/blog/post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/new', name: 'admin_blog_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, BlogUploader $uploader): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($image = $form->get('image')->getData()) {
                $filename = $uploader->uploadPostImage($image);
                $post->setImage($filename);
            }
            $em->persist($post);
            $em->flush();

            $this->addFlash('success', 'Post added.');

            return $this->redirectToRoute('admin_blog_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/blog/post/new.html.twig', [
            'post' => $post,
            'form' => $form,
            'formTag' => $this->createForm(TagType::class, new Tag()),
        ]);
    }

    #[Route('/{id}', name: 'admin_blog_post_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('admin/blog/post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_blog_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $em, BlogUploader $uploader): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($image = $form->get('image')->getData()) {
                $uploader->removePostImage($post->getImage());
                $filename = $uploader->uploadPostImage($image);
                $post->setImage($filename);
            }
            $em->flush();

            $this->addFlash('success', 'Changes saved.');

            return $this->redirectToRoute('admin_blog_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/blog/post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
            'formTag' => $this->createForm(TagType::class, new Tag()),
        ]);
    }

    #[Route('/{id}', name: 'admin_blog_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $em, BlogUploader $uploader): Response
    {
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            $uploader->removePostImage($post->getImage());
            $em->remove($post);
            $em->flush();

            $this->addFlash('success', 'Post deleted.');
        } else {
            $this->addFlash('warning', 'Something went wrong! Try again.');
        }

        return $this->redirectToRoute('admin_blog_post_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/tag/new', name: 'admin_blog_post_tag_new', methods: ['POST'])]
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
                'message' => 'Tag added.',
                'tag' => [
                    'id' => $tag->getId(),
                    'text' => $tag->getName(),
                ],
            ];
        } else {
            $data = [
                'status' => false,
                'message' => 'Something went wrong!',
                'err' => $form->getErrors(),
            ];
        }

        return $this->json($data);
    }
}
