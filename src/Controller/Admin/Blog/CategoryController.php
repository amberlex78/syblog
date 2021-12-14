<?php

namespace App\Controller\Admin\Blog;

use App\Entity\Blog\Category;
use App\Form\Admin\Blog\CategoryType;
use App\Repository\Blog\CategoryRepository;
use App\Service\Blog\CategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/blog/category', name: 'admin_blog_category_')]
class CategoryController extends AbstractController
{
    public function __construct(
        private CategoryService $categoryService
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAllOrdered();

        return $this->render('admin/blog/category/index.html.twig', compact('categories'));
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->handleCreate($form, $category);
            $this->addFlash('success', 'Category created!');

            return $this->redirectToRoute('admin_blog_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/blog/category/new.html.twig', compact('category', 'form'));
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->render('admin/blog/category/show.html.twig', compact('category'));
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Category $category, Request $request): Response
    {
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->handleEdit($form, $category);
            $this->addFlash('success', 'Changes saved!');

            return $this->redirectToRoute('admin_blog_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/blog/category/edit.html.twig', compact('category', 'form'));
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Category $category, Request $request): Response
    {
        if ($category->getPosts()->count()) {
            $this->addFlash('warning', 'Category is not empty!');

            return $this->redirectToRoute('admin_blog_category_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $this->categoryService->handleDelete($category);
            $this->addFlash('success', 'Category saved!');
        } else {
            $this->addFlash('warning', 'Something went wrong! Try again.');
        }

        return $this->redirectToRoute('admin_blog_category_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/image/{id}', name: 'delete_image', methods: ['DELETE'])]
    public function deleteImage(Category $category, Request $request): JsonResponse
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $this->categoryService->handleDeleteImage($category);

            return $this->json(['success' => true]);
        } else {
            return $this->json(['error' => 'Bad Request!'], 400);
        }
    }
}
