<?php

namespace App\Service\Uploader\Blog;

use App\Entity\Blog\Category;
use App\Service\Uploader\FileUploader;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CategoryImageUploader extends FileUploader
{
    public function uploadImage(UploadedFile $uploadedFile): string
    {
        return $this->uploadFile(Category::IMAGES_DIR, $uploadedFile);
    }

    public function removeImage(?string $filename)
    {
        if (!$filename) {
            return;
        }

        $this->removeCache(Category::IMAGES_DIR, $filename);
        $this->removeFile(Category::IMAGES_DIR, $filename);
    }
}
