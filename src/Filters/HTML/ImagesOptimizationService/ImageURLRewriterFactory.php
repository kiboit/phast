<?php


namespace Kibo\Phast\Filters\HTML\ImagesOptimizationService;

use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\Security\ServiceSignatureFactory;
use Kibo\Phast\ValueObjects\URL;

class ImageURLRewriterFactory {
    public function make(array $config, $filterClass = '') {
        $signature = (new ServiceSignatureFactory())->make($config);

        if (isset($config['documents']['filters'][$filterClass])) {
            $classConfig = $config['documents']['filters'][$filterClass];
        } elseif (isset($config['styles']['filters'][$filterClass])) {
            $classConfig = $config['styles']['filters'][$filterClass];
        } else {
            $classConfig = [];
        }

        if (isset($classConfig['serviceUrl'])) {
            $serviceUrl = $classConfig['serviceUrl'];
        } else {
            $serviceUrl = $config['servicesUrl'] . '?service=images';
        }
        return new ImageURLRewriter(
            $signature,
            new LocalRetriever($config['retrieverMap']),
            (new ImageInliningManagerFactory())->make($config),
            URL::fromString($config['documents']['baseUrl']),
            URL::fromString($serviceUrl),
            $config['images']['whitelist']
        );
    }
}
