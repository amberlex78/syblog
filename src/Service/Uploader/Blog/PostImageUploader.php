<?php

namespace App\Service\Uploader\Blog;

use App\Entity\Blog\Post;
use App\Service\Uploader\FileUploader;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PostImageUploader extends FileUploader
{
    public function uploadImage(UploadedFile $uploadedFile): string
    {
        return $this->uploadFile(Post::IMAGES_DIR, $uploadedFile);
    }

    public function removeImage(?string $filename)
    {
        if (!$filename) {
            return;
        }

        $this->removeCache(Post::IMAGES_DIR, $filename);
        $this->removeFile(Post::IMAGES_DIR, $filename);
    }
}
