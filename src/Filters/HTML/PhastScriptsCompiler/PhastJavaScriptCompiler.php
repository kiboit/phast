<?php


namespace Kibo\Phast\Filters\HTML\PhastScriptsCompiler;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\Common\JSMinifier;
use Kibo\Phast\Common\JSON;
use Kibo\Phast\ValueObjects\PhastJavaScript;

class PhastJavaScriptCompiler {

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var \stdClass
     */
    private $lastCompiledConfig;

    /**
     * PhastJavaScriptCompiler constructor.
     * @param Cache $cache
     */
    public function __construct(Cache $cache) {
        $this->cache = $cache;
    }

    /**
     * @return \stdClass|null
     */
    public function getLastCompiledConfig() {
        return $this->lastCompiledConfig;
    }


    /**
     * @param PhastJavaScript[] $scripts
     * @return string
     */
    public function compileScripts(array $scripts) {
        $scripts = array_merge([
            new PhastJavaScript(__DIR__ . '/runner.js'),
            new PhastJavaScript(__DIR__ . '/es6-promise.js'),
            new PhastJavaScript(__DIR__ . '/phast.js')
        ], $scripts);

        return $this->cache->get($this->getCacheKey($scripts), function () use ($scripts) {
            return $this->performCompilation($scripts);
        });
    }

    /**
     * @param PhastJavaScript[] $scripts
     * @return string
     */
    public function compileScriptsWithConfig(array $scripts) {
        $compiled = $this->compileScripts($scripts);
        return '(' . $compiled . ')(' . $this->compileConfig($scripts) . ');';
    }

    /**
     * @param PhastJavaScript[] $scripts
     * @return string
     */
    private function performCompilation(array $scripts) {
        $compiled = implode(',', array_map(function (PhastJavaScript $script) {
            return $this->interpolate($script->getContents());
        }, $scripts));
        $compiled = 'function phastScripts(phast){phast.scripts=[' . $compiled . '];(phast.scripts.shift())();}';
        return (new JSMinifier($compiled))->min();
    }

    /**
     * @param PhastJavaScript[] $scripts
     * @return string
     */
    private function compileConfig(array $scripts) {
        $config = new \stdClass();
        foreach ($scripts as $script) {
            if ($script->hasConfig()) {
                $config->{$script->getConfigKey()} = $script->getConfig();
            }
        }
        $this->lastCompiledConfig = $config;
        return JSON::encode(['config' => $config]);
    }

    /**
     * @param string $script
     * @return string
     */
    private function interpolate($script) {
        return sprintf('(function(){%s})', $script);
    }

    /**
     * @param PhastJavaScript[] $scripts
     * @return string
     */
    private function getCacheKey(array $scripts) {
        return array_reduce($scripts, function ($carry, PhastJavaScript $script) {
            $carry .= $script->getFilename() . '-' . $script->getCacheSalt() . "\n";
            return $carry;
        }, '');
    }

}
