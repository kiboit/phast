<?php

namespace Kibo\Phast\Services\Scripts;

use Kibo\Phast\Filters\JavaScript\Minification\JSMinifierFilter;
use Kibo\Phast\Filters\Service\CompositeFilter;
use Kibo\Phast\Filters\Text\Decode\Filter as DecodeFilter;
use Kibo\Phast\Security\ServiceSignatureFactory;
use Kibo\Phast\Services\ServiceFactoryTrait;

class Factory {
    use ServiceFactoryTrait;

    public function make(array $config) {
        $cachedComposite = $this->makeFilter($config);

        return new Service(
            (new ServiceSignatureFactory())->make($config),
            $config['scripts']['whitelist'],
            $this->makeRetriever($config),
            $this->makeCachingServiceFilter($config, $cachedComposite, 'scripts-minified'),
            $config
        );
    }

    public function makeRetriever(array $config) {
        return $this->makeUniversalCachingRetriever($config, 'scripts');
    }

    public function makeFilter(array $config) {
        $filter = new CompositeFilter();
        $filter->addFilter(new DecodeFilter());
        $filter->addFilter(new JSMinifierFilter(@$config['scripts']['removeLicenseHeaders']));
        return $filter;
    }
}
