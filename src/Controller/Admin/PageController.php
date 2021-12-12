<?php

namespace App\Controller\Admin;

use App\Entity\Page;
use App\Form\Admin\PageType;
use App\Repository\PageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/page', name: 'admin_page_')]
class PageController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(PageRepository $pageRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $pages = $paginator->paginate(
            $pageRepository->findAll(),
            $request->query->getInt('page', 1)
        );

        return $this->render('admin/page/index.html.twig', compact('pages'));
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $em, Request $request): Response
    {
        $page = new Page();
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($page);
            $em->flush();

            $this->addFlash('success', 'Page created!');

            return $this->redirectToRoute('admin_page_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/page/new.html.twig', compact('page', 'form'));
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Page $page): Response
    {
        return $this->render('admin/page/show.html.twig', compact('page'));
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(EntityManagerInterface $em, Request $request, Page $page): Response
    {
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Changes saved!');

            return $this->redirectToRoute('admin_page_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/page/edit.html.twig', compact('page', 'form'));
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(EntityManagerInterface $em, Request $request, Page $page): Response
    {
        if ($this->isCsrfTokenValid('delete' . $page->getId(), $request->request->get('_token'))) {
            $em->remove($page);
            $em->flush();

            $this->addFlash('success', 'Page deleted!');
        } else {
            $this->addFlash('warning', 'Something went wrong! Try again.');
        }

        return $this->redirectToRoute('admin_page_index', [], Response::HTTP_SEE_OTHER);
    }
}
