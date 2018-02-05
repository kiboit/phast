<?php


namespace Kibo\Phast\Common;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\ValueObjects\PhastJavaScript;

class PhastJavaScriptCompiler {

    /**
     * @var Cache
     */
    private $cache;

    /**
     * PhastJavaScriptCompiler constructor.
     * @param Cache $cache
     */
    public function __construct(Cache $cache) {
        $this->cache = $cache;
    }


    /**
     * @param PhastJavaScript[] $scripts
     * @return string
     */
    public function compileScripts(array $scripts) {
        return $this->cache->get($this->getCacheKey($scripts), function () use ($scripts) {
            return $this->performCompilation($scripts);
        });
    }

    /**
     * @param PhastJavaScript[] $scripts
     * @return string
     */
    private function performCompilation(array $scripts) {
        $compiled = array_reduce($scripts, function ($carry, PhastJavaScript $script) {
            $carry .= $this->interpolate($script->getContents());
            return $carry;
        }, '');
        $compiled = $this->interpolate($compiled);
        return (new JSMinifier($compiled))->min();
    }

    /**
     * @param string $script
     * @return string
     */
    private function interpolate($script) {
        return sprintf('(function(){%s})();', $script);
    }

    /**
     * @param PhastJavaScript[] $scripts
     * @return string
     */
    private function getCacheKey(array $scripts) {
        return array_reduce($scripts, function ($carry, PhastJavaScript $script) {
            $carry .= $script->getFilename();
            return $carry;
        }, '');
    }

}
