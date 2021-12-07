<?php

namespace App\Controller\Admin\Blog;

use App\Entity\Blog\Post;
use App\Entity\Blog\Tag;
use App\Form\Admin\Blog\PostType;
use App\Form\Admin\Blog\TagType;
use App\Service\Admin\Blog\PostService;
use App\Service\Uploader\Blog\PostImageUploader;
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
        private EntityManagerInterface $em,
        private PostImageUploader $uploader,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(PostService $postService, Request $request, PaginatorInterface $paginator): Response
    {
        $posts = $postService->findAllFiltered(
            $request->query->getInt('category'),
            $request->query->getInt('tag')
        );

        $posts = $paginator->paginate($posts, $request->query->getInt('page', 1));

        return $this->render('admin/blog/post/index.html.twig', compact('posts'));
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($image = $form->get('image')->getData()) {
                $filename = $this->uploader->uploadImage($image);
                $post->setImage($filename);
            }
            $this->em->persist($post);
            $this->em->flush();

            $this->addFlash('success', 'Post added.');

            return $this->redirectToRoute('admin_blog_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/blog/post/new.html.twig', [
            'post' => $post,
            'form' => $form,
            'formTag' => $this->createForm(TagType::class, new Tag()),
        ]);
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
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($image = $form->get('image')->getData()) {
                $this->uploader->removeImage($post->getImage());
                $filename = $this->uploader->uploadImage($image);
                $post->setImage($filename);
            }
            $this->em->flush();

            $this->addFlash('success', 'Changes saved.');

            return $this->redirectToRoute('admin_blog_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/blog/post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
            'formTag' => $this->createForm(TagType::class, new Tag()),
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Post $post, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            $this->uploader->removeImage($post->getImage());
            $this->em->remove($post);
            $this->em->flush();

            $this->addFlash('success', 'Post deleted.');
        } else {
            $this->addFlash('warning', 'Something went wrong! Try again.');
        }

        return $this->redirectToRoute('admin_blog_post_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/image/{id}', name: 'delete_image', methods: ['DELETE'])]
    public function deleteImage(Post $post, Request $request): JsonResponse
    {
        if ($request->isXmlHttpRequest()
            && $this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))
        ) {
            $this->uploader->removeImage($post->getImage());
            $post->setImage(null);
            $this->em->flush();

            return $this->json(['success' => true]);
        } else {
            return $this->json(['error' => 'Bad Request!'], 400);
        }
    }

    #[Route('/tag/new', name: 'tag_new', methods: ['POST'])]
    public function tagNew(Request $request): JsonResponse
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($tag);
            $this->em->flush();
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
