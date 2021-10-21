<?php

namespace App\Service\Uploader;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class BlogUploader extends BaseUploader
{
    public const IMAGES_DIR = 'blog/post_images';
    public const CATEGORY_IMAGES_DIR = 'blog/category_images';

    public function uploadPostImage(UploadedFile $uploadedFile): string
    {
        return $this->baseUploadFile(self::IMAGES_DIR, $uploadedFile);
    }

    public function removePostImage(?string $filename)
    {
        $this->baseRemoveFile(self::IMAGES_DIR, $filename);
    }

    public function uploadCategoryImage(UploadedFile $uploadedFile): string
    {
        return $this->baseUploadFile(self::CATEGORY_IMAGES_DIR, $uploadedFile);
    }

    public function removeCategoryImage(?string $filename)
    {
        $this->baseRemoveFile(self::CATEGORY_IMAGES_DIR, $filename);
    }
}
