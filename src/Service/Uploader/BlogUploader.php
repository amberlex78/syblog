<?php

namespace App\Service\Uploader;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class BlogUploader extends BaseUploader
{
    public const IMAGES_DIR = 'blog/post_images';

    public function uploadPostImage(UploadedFile $uploadedFile): string
    {
        return $this->baseUploadFile(self::IMAGES_DIR, $uploadedFile);
    }

    public function removePostImage(?string $filename)
    {
        $this->baseRemoveFile(self::IMAGES_DIR, $filename);
    }
}
