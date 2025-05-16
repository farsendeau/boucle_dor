<?php

namespace App\Twig\Runtime;

use App\Entity\Site\Site;
use App\Repository\Site\SiteRepository;
use Doctrine\Common\Collections\Collection;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Twig\Extension\RuntimeExtensionInterface;
use Vich\UploaderBundle\Entity\File;

readonly class SiteInfoExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private SiteRepository $siteRepository,
        private CacheInterface $cache,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getInfo(string $value): string|File|array|null
    {
        $site = $this->cache->get('site', function () {
            $site = $this->siteRepository->findOneBy([]);
            // Forcer le chargement des relations
            $site->getLinks()->initialize();
            return $site;
        });

        if (!$site) {
            return null;
        }


        if ('mapLocalization' === $value) {
            return [
                'site' => $site,
                'map_url' => $site->getMapUrl(),
                'map_image' => $site->getMapImage(),
            ];
        }

        $method = sprintf('get%s', ucfirst($value));;
        if (!method_exists(Site::class, $method)) {
            return null;
        }

        $result = $site->$method();

        // Si c'est une Collection Doctrine, on la convertit en tableau
        if ($result instanceof Collection) {
            return $result->toArray();
        }

        return $result;
    }
}
