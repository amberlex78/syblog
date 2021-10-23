<?php

namespace App\Controller\Admin\Blog;

use App\Entity\Blog\Tag;
use App\Form\Admin\Blog\TagType;
use App\Repository\Blog\TagRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/blog/tag')]
class TagController extends AbstractController
{
    #[Route('/', name: 'admin_blog_tag_index', methods: ['GET'])]
    public function index(TagRepository $tagRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $tags = $paginator->paginate(
            $tagRepository->findBy([], ['id' => 'desc']),
            $request->query->getInt('page', 1)
        );

        return $this->render('admin/blog/tag/index.html.twig', [
            'tags' => $tags,
        ]);
    }

    #[Route('/new', name: 'admin_blog_tag_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tag);
            $entityManager->flush();

            return $this->redirectToRoute('admin_blog_tag_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/blog/tag/new.html.twig', [
            'tag' => $tag,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_blog_tag_show', methods: ['GET'])]
    public function show(Tag $tag): Response
    {
        return $this->render('admin/blog/tag/show.html.twig', [
            'tag' => $tag,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_blog_tag_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tag $tag): Response
    {
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_blog_tag_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/blog/tag/edit.html.twig', [
            'tag' => $tag,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_blog_tag_delete', methods: ['POST'])]
    public function delete(Request $request, Tag $tag): Response
    {
        if ($this->isCsrfTokenValid('delete' . $tag->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($tag);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_blog_tag_index', [], Response::HTTP_SEE_OTHER);
    }
}
