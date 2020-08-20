<?php
namespace Kibo\Phast\Filters\HTML\PhastScriptsCompiler;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\Common\JSON;
use Kibo\Phast\Services\Bundler\ShortBundlerParamsParser;
use Kibo\Phast\Services\ServiceRequest;
use Kibo\Phast\ValueObjects\PhastJavaScript;
use Kibo\Phast\ValueObjects\URL;

class PhastJavaScriptCompiler {
    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var string
     */
    private $serviceUrl;

    private $serviceRequestFormat;

    /**
     * @var \stdClass
     */
    private $lastCompiledConfig;

    /**
     * PhastJavaScriptCompiler constructor.
     * @param Cache $cache
     * @param string $serviceUrl
     */
    public function __construct(Cache $cache, $serviceUrl, $serviceRequestFormat) {
        $this->cache = $cache;
        $this->serviceUrl = (new ServiceRequest())
            ->withUrl(URL::fromString((string) $serviceUrl))
            ->serialize(ServiceRequest::FORMAT_QUERY);
        $this->serviceRequestFormat = $serviceRequestFormat;
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
        return $this->cache->get($this->getCacheKey($scripts), function () use ($scripts) {
            return $this->performCompilation($scripts);
        });
    }

    /**
     * @param PhastJavaScript[] $scripts
     * @return string
     */
    public function compileScriptsWithConfig(array $scripts) {
        $bundlerMappings = ShortBundlerParamsParser::getParamsMappings();
        $jsMappings = array_combine(array_values($bundlerMappings), array_keys($bundlerMappings));
        $resourcesLoader = PhastJavaScript::fromFile(__DIR__ . '/resources-loader.js');
        $resourcesLoader->setConfig('resourcesLoader', [
            'serviceUrl' => (string) $this->serviceUrl,
            'shortParamsMappings' => $jsMappings,
            'pathInfo' => $this->serviceRequestFormat === ServiceRequest::FORMAT_PATH,
        ]);
        $scripts = array_merge([
            PhastJavaScript::fromFile(__DIR__ . '/runner.js'),
            PhastJavaScript::fromFile(__DIR__ . '/es6-promise.js'),
            PhastJavaScript::fromFile(__DIR__ . '/hash.js'),
            PhastJavaScript::fromFile(__DIR__ . '/service-url.js'),
            $resourcesLoader,
            PhastJavaScript::fromFile(__DIR__ . '/phast.js'),
        ], $scripts);
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
        return 'function phastScripts(phast){phast.scripts=[' . $compiled . '];(phast.scripts.shift())();}';
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
        return JSON::encode(['config' => base64_encode(JSON::encode($config))]);
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
