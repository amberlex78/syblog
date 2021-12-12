<?php

namespace App\Controller\Admin\Blog;

use App\Entity\Blog\Tag;
use App\Form\Admin\Blog\TagType;
use App\Repository\Blog\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/blog/tag', name: 'admin_blog_tag_')]
class TagController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(TagRepository $tagRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $tags = $paginator->paginate(
            $tagRepository->findAllOrderedByNewest(),
            $request->query->getInt('page', 1)
        );

        return $this->render('admin/blog/tag/index.html.twig', compact('tags'));
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($tag);
            $this->em->flush();

            $this->addFlash('success', 'Tag created!');

            return $this->redirectToRoute('admin_blog_tag_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/blog/tag/new.html.twig', compact('tag', 'form'));
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Tag $tag): Response
    {
        return $this->render('admin/blog/tag/show.html.twig', compact('tag'));
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Tag $tag, Request $request): Response
    {
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', 'Changes saved!');

            return $this->redirectToRoute('admin_blog_tag_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/blog/tag/edit.html.twig', compact('tag', 'form'));
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Tag $tag, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $tag->getId(), $request->request->get('_token'))) {
            $this->em->remove($tag);
            $this->em->flush();

            $this->addFlash('success', 'Tag deleted!');
        } else {
            $this->addFlash('warning', 'Something went wrong! Try again.');
        }

        return $this->redirectToRoute('admin_blog_tag_index', [], Response::HTTP_SEE_OTHER);
    }
}
