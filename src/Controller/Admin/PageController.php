<?php

namespace App\Controller\Admin;

use App\Entity\Page;
use App\Form\Admin\PageType;
use App\Repository\PageRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/page')]
class PageController extends AbstractController
{
    #[Route('/', name: 'admin_page_index', methods: ['GET'])]
    public function index(PageRepository $pageRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $pages = $paginator->paginate(
            $pageRepository->findAll(),
            $request->query->getInt('page', 1)
        );

        return $this->render('admin/page/index.html.twig', [
            'pages' => $pages,
        ]);
    }

    #[Route('/new', name: 'admin_page_new', methods: ['GET','POST'])]
    public function new(Request $request): Response
    {
        $page = new Page();
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($page);
            $entityManager->flush();

            $this->addFlash('success', 'Page added.');

            return $this->redirectToRoute('admin_page_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/page/new.html.twig', [
            'page' => $page,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_page_show', methods: ['GET'])]
    public function show(Page $page): Response
    {
        return $this->render('admin/page/show.html.twig', [
            'page' => $page,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_page_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Page $page): Response
    {
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Changes saved.');

            return $this->redirectToRoute('admin_page_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/page/edit.html.twig', [
            'page' => $page,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_page_delete', methods: ['POST'])]
    public function delete(Request $request, Page $page): Response
    {
        if ($this->isCsrfTokenValid('delete'.$page->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($page);
            $entityManager->flush();
            $this->addFlash('success', 'Page deleted.');
        } else {
            $this->addFlash('warning', 'Something went wrong! Try again.');
        }

        return $this->redirectToRoute('admin_page_index', [], Response::HTTP_SEE_OTHER);
    }
}
