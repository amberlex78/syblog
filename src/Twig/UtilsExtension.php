<?php

namespace App\Twig;

use Symfony\Component\HttpKernel\Kernel;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UtilsExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('phpVersion', [$this, 'phpVersion']),
            new TwigFunction('symfonyVersion', [$this, 'symfonyVersion']),
        ];
    }

    public function phpVersion(): string
    {
        return PHP_VERSION;
    }

    public function symfonyVersion(): string
    {
        return Kernel::VERSION;
    }
}
