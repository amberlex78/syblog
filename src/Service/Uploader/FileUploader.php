<?php

namespace App\Service\Uploader;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    public function __construct(
        protected string $uploadPath,
        protected CacheManager $cacheManager,
        private Filesystem $filesystem,
        private RequestStackContext $requestStackContext,
    ) {
    }

    public function getPublicPath(string $path): string
    {
        // RequestStackContext needed if you deploy under a subdirectory
        return $this->requestStackContext->getBasePath() . '/uploads/' . $path;
    }

    public function uploadFile(string $filesDir, UploadedFile $uploadedFile): string
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

    public function removeFile(string $filesDir, ?string $filename)
    {
        if (!$filename) {
            return;
        }

        $fullPathToFile = $this->uploadPath . '/' . $filesDir . '/' . $filename;

        try {
            $this->filesystem->remove($fullPathToFile);
        } catch (IOException $exception) {
            echo $exception->getMessage();
        }
    }

    protected function removeCache(string $imgDir, string $filename)
    {
        $image = $this->getPublicPath($imgDir . '/' . $filename);
        $this->cacheManager->remove($image);
        $this->cacheManager->remove($image . '.webp');

    }
}
