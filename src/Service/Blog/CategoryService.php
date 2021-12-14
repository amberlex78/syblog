<?php

namespace App\Service\Blog;

use App\Entity\Blog\Category;
use App\Service\Uploader\Blog\CategoryImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;

class CategoryService
{
    public function __construct(
        private EntityManagerInterface $em,
        private CategoryImageUploader $uploader,
    ) {
    }

    public function handleCreate(FormInterface $form, Category $category)
    {
        if ($image = $form->get('image')->getData()) {
            $filename = $this->uploader->uploadImage($image);
            $category->setImage($filename);
        }
        $this->em->persist($category);
        $this->em->flush();
    }

    public function handleEdit(FormInterface $form, Category $category)
    {
        if ($image = $form->get('image')->getData()) {
            $this->uploader->removeImage($category->getImage());
            $filename = $this->uploader->uploadImage($image);
            $category->setImage($filename);
        }
        $this->em->flush();
    }

    public function handleDelete(Category $category)
    {
        $this->uploader->removeImage($category->getImage());
        $this->em->remove($category);
        $this->em->flush();
    }

    public function handleDeleteImage(Category $category)
    {
        $this->uploader->removeImage($category->getImage());
        $category->setImage(null);
        $this->em->flush();
    }
}
