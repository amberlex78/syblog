<?php

namespace App\Twig;

use App\Service\Uploader\BaseUploader;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension implements ServiceSubscriberInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('asset_uploaded', [$this, 'getAssetUploadedPath']),
        ];
    }

    public function getAssetUploadedPath(string $path): string
    {
        return $this->container
            ->get(BaseUploader::class)
            ->getPublicPath($path);
    }

    public static function getSubscribedServices(): array
    {
        return [
            BaseUploader::class,
        ];
    }
}
