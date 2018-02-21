<?php


namespace Kibo\Phast\Common;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\ValueObjects\PhastJavaScript;

// TODO: Move to Kibo\Phast\Filters\HTML\PhastScriptsCompiler
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
        array_unshift($scripts, new PhastJavaScript(__DIR__ . '/phast-js-env.js'));
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
        return json_encode(['config' => $config]);
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
            $carry .= $script->getFilename() . '-' . $script->getLastModificationTime() . "\n";
            return $carry;
        }, '');
    }

}
