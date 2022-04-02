<?php

namespace Kibo\Phast\Filters\HTML\CSSInlining;

use Kibo\Phast\Cache\Sqlite\Cache;

class OptimizerFactory {
    /**
     * @var Cache
     */
    private $cache;

    public function __construct(array $config) {
        $this->cache = new Cache($config['cache'], 'css-optimizitor');
    }

    /**
     * @param \Traversable $elements
     * @return Optimizer
     */
    public function makeForElements(\Traversable $elements) {
        return new Optimizer($elements, $this->cache);
    }
}
