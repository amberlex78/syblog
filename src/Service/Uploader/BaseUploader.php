<?php

namespace App\Service\Uploader;

use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

// todo: improve
class BaseUploader
{
    protected string $uploadPath;
    protected Filesystem $filesystem;
    private RequestStackContext $requestStackContext;

    public function __construct(string $uploadPath, Filesystem $filesystem, RequestStackContext $requestStackContext)
    {
        $this->uploadPath = $uploadPath;
        $this->filesystem = $filesystem;
        $this->requestStackContext = $requestStackContext;
    }

    public function getPublicPath(string $path): string
    {
        // Needed if you deploy under a subdirectory
        return $this->requestStackContext->getBasePath() . '/uploads/' . $path;
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
