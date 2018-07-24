<?php

namespace Kibo\Phast\Filters\HTML\ScriptsProxyService;

use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Filters\HTML\BaseHTMLStreamFilter;
use Kibo\Phast\Filters\HTML\Helpers\JSDetectorTrait;
use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\Services\Bundler\ServiceParams;
use Kibo\Phast\Services\ServiceRequest;
use Kibo\Phast\ValueObjects\PhastJavaScript;
use Kibo\Phast\ValueObjects\URL;

class Filter extends BaseHTMLStreamFilter {
    use JSDetectorTrait, LoggingTrait;

    /**
     * @var array
     */
    private $config;

    /**
     * @var ServiceSignature
     */
    private $signature;

    /**
     * @var Retriever
     */
    private $retriever;

    /**
     * @var ObjectifiedFunctions
     */
    private $functions;

    /**
     * @var bool
     */
    private $didInject = false;

    /**
     * Filter constructor.
     * @param array $config
     * @param ServiceSignature $signature
     * @param Retriever $retriever
     * @param ObjectifiedFunctions $functions
     */
    public function __construct(
        array $config,
        ServiceSignature $signature,
        Retriever $retriever,
        ObjectifiedFunctions $functions = null
    ) {
        $this->config = $config;
        $this->signature = $signature;
        $this->retriever = $retriever;
        $this->functions = is_null($functions) ? new ObjectifiedFunctions() : $functions;
    }


    protected function isTagOfInterest(Tag $tag) {
        return $tag->getTagName() == 'script' && $this->isJSElement($tag);
    }

    protected function handleTag(Tag $script) {
        $this->rewriteScriptSource($script);
        if (!$this->didInject) {
            $this->addScript();
            $this->didInject = true;
        }
        yield $script;
    }

    private function rewriteScriptSource(Tag $element) {
        if (!$element->hasAttribute('src')) {
            return;
        }
        $src = trim($element->getAttribute('src'));
        $absolute = $this->getAbsoluteURL($src);
        if (!$this->shouldProxyURL($absolute)) {
            $this->logger()->info('Not proxying {src}', ['src' => $src]);
            return;
        }
        $this->logger()->info('Proxying {src}', ['src' => $src]);
        $rewritten = $this->makeProxiedURL($absolute);
        $element->setAttribute('src', $rewritten);
        $element->setAttribute('data-phast-original-src', $src);
        $element->setAttribute('data-phast-params', $this->makeServiceParams($absolute));
    }

    private function makeProxiedURL($url) {
        $params = [
            'src' => (string) $url,
            'cacheMarker' => $this->retriever->getCacheSalt($url)
        ];
        return (new ServiceRequest())
            ->withUrl(URL::fromString($this->config['serviceUrl']))
            ->withParams($params)
            ->serialize();
    }

    private function makeServiceParams($url) {
        return ServiceParams::
            fromArray(['src' => (string) $url, 'isScript' => '1'])
            ->sign($this->signature)
            ->serialize();
    }

    private function shouldProxyURL(URL $url) {
        if ($url->isLocalTo($this->context->getBaseUrl())) {
            return true;
        }
        $str = (string) $url;
        foreach ($this->config['match'] as $pattern) {
            if (preg_match($pattern, $str)) {
                return true;
            }
        }
        return false;
    }

    private function addScript() {
        $config = [
            'serviceUrl' => $this->config['serviceUrl'],
            'urlRefreshTime' => $this->config['urlRefreshTime'],
            'whitelist' => $this->config['match']
        ];
        $script = new PhastJavaScript(__DIR__ . '/rewrite-function.js');
        $script->setConfig('script-proxy-service', $config);
        $this->context->addPhastJavaScript($script);
    }

    private function getAbsoluteURL($url) {
        return URL::fromString($url)->withBase($this->context->getBaseUrl());
    }

}
