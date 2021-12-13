<?php

namespace App\Service\Admin\Blog;

use App\Entity\Blog\Post;
use App\Repository\Blog\CategoryRepository;
use App\Repository\Blog\PostRepository;
use App\Repository\Blog\TagRepository;
use App\Repository\UserRepository;
use App\Service\Uploader\Blog\PostImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;

class PostService
{
    public function __construct(
        private EntityManagerInterface $em,
        private PostImageUploader $uploader,
        private PostRepository $postRepository,
        private CategoryRepository $categoryRepository,
        private TagRepository $tagRepository,
        private UserRepository $userRepository,
    ) {
    }

    // todo: $category->getPosts(), $tag->getPosts(), $user->getPosts() move to PostRepository via join or fetch: 'EAGER'
    public function findAllFiltered(?int $category, ?int $tag, ?int $user): mixed
    {
        if ($category) {
            $category = $this->categoryRepository->find($category);
            $posts = $category ? $category->getPosts() : $this->postRepository->findAllOrderedByNewest();
        } elseif ($tag) {
            $tag = $this->tagRepository->find($tag);
            $posts = $tag ? $tag->getPosts() : $this->postRepository->findAllOrderedByNewest();
        } elseif ($user) {
            $user = $this->userRepository->find($user);
            $posts = $user ? $user->getPosts() : $this->postRepository->findAllOrderedByNewest();
        } else {
            $posts = $this->postRepository->findAllOrderedByNewest();
        }

        return $posts;
    }

    public function handleCreate(FormInterface $form, Post $post)
    {
        if ($image = $form->get('image')->getData()) {
            $filename = $this->uploader->uploadImage($image);
            $post->setImage($filename);
        }
        $post->setUser($this->getUser());
        $this->em->persist($post);
        $this->em->flush();
    }

    public function handleEdit(FormInterface $form, Post $post)
    {
        if ($image = $form->get('image')->getData()) {
            $this->uploader->removeImage($post->getImage());
            $filename = $this->uploader->uploadImage($image);
            $post->setImage($filename);
        }
        $this->em->flush();
    }

    public function handleDelete(Post $post)
    {
        $this->uploader->removeImage($post->getImage());
        $this->em->remove($post);
        $this->em->flush();
    }

    public function handleDeleteImage(Post $post)
    {
        $this->uploader->removeImage($post->getImage());
        $post->setImage(null);
        $this->em->flush();
    }
}
