<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\LegalPageExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LegalPageExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('legalPageIsDefined', [LegalPageExtensionRuntime::class, 'isDefined']),
            new TwigFunction('legalPage', [LegalPageExtensionRuntime::class, 'getPage']),
        ];
    }
}
