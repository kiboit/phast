<?php

namespace Kibo\Phast\Filters\HTML\CSSInlining;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\Cache\Factory as CacheFactory;

class OptimizerFactory {
    /**
     * @var Cache
     */
    private $cache;

    public function __construct(array $config) {
        $this->cache = (new CacheFactory($config['cache']))->getCache('css-optimizitor');
    }

    /**
     * @param \Traversable $elements
     * @return Optimizer
     */
    public function makeForElements(\Traversable $elements) {
        return new Optimizer($elements, $this->cache);
    }
}
