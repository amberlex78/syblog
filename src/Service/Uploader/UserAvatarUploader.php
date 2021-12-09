<?php

namespace App\Service\Uploader;

use App\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserAvatarUploader extends FileUploader
{
    public function uploadImage(UploadedFile $uploadedFile): string
    {
        return $this->uploadFile(User::IMAGES_DIR, $uploadedFile);
    }

    public function removeImage(?string $filename)
    {
        if (!$filename) {
            return;
        }

        $this->removeCache(User::IMAGES_DIR, $filename);
        $this->removeFile(User::IMAGES_DIR, $filename);
    }
}
