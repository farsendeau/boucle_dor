<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\SiteInfoExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SiteInfoExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('siteGetInfo', [SiteInfoExtensionRuntime::class, 'getInfo']),
        ];
    }
}
