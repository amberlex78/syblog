<?php

namespace App\Controller\Admin\Blog;

use App\Entity\Blog\Category;
use App\Form\Admin\Blog\CategoryType;
use App\Repository\Blog\CategoryRepository;
use App\Service\Uploader\BlogUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/blog/category')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'admin_blog_category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('admin/blog/category/index.html.twig', [
            'categories' => $categoryRepository->findAllOrdered(),
        ]);
    }

    #[Route('/new', name: 'admin_blog_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, BlogUploader $uploader): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($image = $form->get('image')->getData()) {
                $filename = $uploader->uploadCategoryImage($image);
                $category->setImage($filename);
            }
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'Category added.');

            return $this->redirectToRoute('admin_blog_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/blog/category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_blog_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->render('admin/blog/category/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_blog_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, EntityManagerInterface $em, BlogUploader $uploader): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($image = $form->get('image')->getData()) {
                $uploader->removeCategoryImage($category->getImage());
                $filename = $uploader->uploadCategoryImage($image);
                $category->setImage($filename);
            }
            $em->flush();

            $this->addFlash('success', 'Changes saved.');

            return $this->redirectToRoute('admin_blog_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/blog/category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_blog_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, EntityManagerInterface $em, BlogUploader $uploader): Response
    {
        if ($category->getPosts()->count()) {
            $this->addFlash('warning', 'Category is not empty!');

            return $this->redirectToRoute('admin_blog_category_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $uploader->removeCategoryImage($category->getImage());
            $em->remove($category);
            $em->flush();

            $this->addFlash('success', 'Category deleted.');
        } else {
            $this->addFlash('warning', 'Something went wrong! Try again.');
        }

        return $this->redirectToRoute('admin_blog_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
