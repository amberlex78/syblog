<?php

namespace App\Service\Admin\Blog;

use App\Repository\Blog\CategoryRepository;
use App\Repository\Blog\PostRepository;
use App\Repository\Blog\TagRepository;

class PostService
{
    public function __construct(
        private PostRepository $postRepository,
        private CategoryRepository $categoryRepository,
        private TagRepository $tagRepository,
    ) {
    }

    // todo: $category->getPosts(), $tag->getPosts() to PostRepository or fetch: 'EAGER'
    public function findAllFiltered(?int $category, ?int $tag): mixed
    {
        if ($category) {
            $category = $this->categoryRepository->find($category);
            $posts = $category ? $category->getPosts() : $this->postRepository->findAllOrderedByNewest();
        } elseif ($tag) {
            $tag = $this->tagRepository->find($tag);
            $posts = $tag ? $tag->getPosts() : $this->postRepository->findAllOrderedByNewest();
        } else {
            $posts = $this->postRepository->findAllOrderedByNewest();
        }

        return $posts;
    }
}
