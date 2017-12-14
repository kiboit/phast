<?php

namespace Kibo\Phast\Factories\Services;

use Kibo\Phast\Cache\FileCache;
use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\Factories\Filters\Image\CompositeImageFilterFactory;
use Kibo\Phast\Factories\Filters\Image\ImageFactory;
use Kibo\Phast\Factories\Security\ServiceSignatureFactory;
use Kibo\Phast\Filters\HTML\ScriptProxyServiceHTMLFilter;
use Kibo\Phast\Retrievers\CachingRetriever;
use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\Retrievers\RemoteRetriever;
use Kibo\Phast\Retrievers\UniversalRetriever;
use Kibo\Phast\Services\CSSProxyService;
use Kibo\Phast\Services\ImageFilteringService;
use Kibo\Phast\Services\ScriptsProxyService;
use Kibo\Phast\Services\Service;

class ServicesFactory {

    /**
     * @param string $service
     * @param array $config
     * @return Service
     * @throws ItemNotFoundException
     */
    public function make($service, array $config) {
        if (!ctype_alpha($service)) {
            throw new ItemNotFoundException('Bad service');
        }
        $class = __NAMESPACE__ . '\\' . ucfirst($service) . 'ServiceFactory';
        if (class_exists($class)) {
            return (new $class())->make($config);
        }
        throw new ItemNotFoundException('Unknown service');
    }
}
