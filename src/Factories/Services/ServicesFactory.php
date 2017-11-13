<?php

namespace Kibo\Phast\Factories\Services;

use Kibo\Phast\Cache\FileCache;
use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\Factories\Filters\Image\CompositeImageFilterFactory;
use Kibo\Phast\Factories\Filters\Image\ImageFactory;
use Kibo\Phast\Factories\Security\ServiceSignatureFactory;
use Kibo\Phast\Filters\HTML\CSSInliningHTMLFilter;
use Kibo\Phast\Filters\HTML\ScriptProxyServiceHTMLFilter;
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
        $method = 'make' . ucfirst($service) . 'Service';
        if (method_exists($this, $method)) {
            return $this->$method($config);
        }
        throw new ItemNotFoundException('Unknown service');
    }

    /**
     * @param array $config
     * @return ImageFilteringService
     */
    public function makeImagesService(array $config) {
        return new ImageFilteringService(
            (new ServiceSignatureFactory())->make($config),
            $config['images']['whitelist'],
            new ImageFactory($config),
            new CompositeImageFilterFactory($config)
        );
    }

    /**
     * @param array $config
     * @return ScriptsProxyService
     */
    public function makeScriptsService(array $config) {
        $retriever = new UniversalRetriever();
        $retriever->addRetriever(new LocalRetriever($config['retrieverMap']));
        $retriever->addRetriever(new RemoteRetriever());
        return new ScriptsProxyService(
            (new ServiceSignatureFactory())->make($config),
            $config['documents']['filters'][ScriptProxyServiceHTMLFilter::class]['match'],
            $retriever,
            new FileCache($config['cache'], 'scripts')
        );
    }

    /**
     * @param array $config
     * @return CSSProxyService
     */
    public function makeCssService(array $config) {
        $retriever = new UniversalRetriever();
        $retriever->addRetriever(new LocalRetriever($config['retrieverMap']));
        $retriever->addRetriever(new RemoteRetriever());
        return new CSSProxyService(
            (new ServiceSignatureFactory())->make($config),
            array_keys($config['documents']['filters'][CSSInliningHTMLFilter::class]['whitelist']),
            $retriever,
            new FileCache($config['cache'], 'css')
        );
    }

}
