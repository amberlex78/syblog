<?php

namespace App\Service\Uploader;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class BaseUploader
{
    protected string $uploadPath;
    protected Filesystem $filesystem;

    public function __construct(string $uploadPath, Filesystem $filesystem)
    {
        $this->uploadPath = $uploadPath;
        $this->filesystem = $filesystem;
    }

    public function getPublicPath(string $path): string
    {
        return '/uploads/' . $path;
    }

    protected function baseUploadFile(string $filesDir, UploadedFile $uploadedFile): string
    {
        $destination = $this->uploadPath . '/' . $filesDir;
        $newFilename = time() . '_' . uniqid() . '.' . $uploadedFile->guessExtension();

        try {
            $uploadedFile->move($destination, $newFilename);
        } catch (FileException $exception) {
            echo $exception->getMessage();
        }

        return $newFilename;
    }

    protected function baseRemoveFile(string $filesDir, ?string $filename)
    {
        if (!$filename) {
            return;
        }

        $fullPathFilename = $this->uploadPath . '/' . $filesDir . '/' . $filename;

        try {
            $this->filesystem->remove($fullPathFilename);
        } catch (IOException $exception) {
            echo $exception->getMessage();
        }
    }
}
