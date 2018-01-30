<?php

namespace Kibo\Phast\Filters\HTML\CSSInlining;


use Kibo\Phast\Cache\File\Cache;
use Kibo\Phast\Common\DOMDocument;

class OptimizerFactory {

    /**
     * @var Cache
     */
    private $cache;

    public function __construct(array $config) {
        $this->cache = new Cache($config['cache'], 'css-optimizitor');
    }

    /**
     * @param DOMDocument $document
     * @return Optimizer
     */
    public function makeForDocument(DOMDocument $document) {
        return new Optimizer($document, $this->cache);
    }

}
