<?php

namespace App\Twig\Runtime;

use App\Entity\Site\Site;
use App\Repository\Site\LegalPageRepository;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Twig\Extension\RuntimeExtensionInterface;

readonly class LegalPageExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private LegalPageRepository $legalContentRepository,
        private CacheInterface      $cache,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function isDefined(string $value, string $locale): bool
    {
        $legalPage = $this->cache->get('legal_page', fn () => $this->legalContentRepository->findOneBy([]));

        $methodTitle = sprintf('get%sTitle%s', ucfirst($value), ucfirst($locale));
        $methodContent = sprintf('get%sContent%s', ucfirst($value), ucfirst($locale));

        return $legalPage->$methodTitle() && $legalPage->$methodContent();
    }

    public function getContent(string $value, string $locale): ?string
    {
        $legalPage = $this->cache->get('legal_page', fn () => $this->legalContentRepository->findOneBy([]));

        if (!$legalPage) {
            return null;
        }

        $method = sprintf('get%s%s', ucfirst($value), ucfirst($locale));
        if (!method_exists(Site::class, $method)) {
            return null;
        }

        return $method->$method();
    }
}
