<?php

namespace Kibo\Phast\Filters\HTML\ScriptsProxyService;

use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Filters\HTML\BaseHTMLStreamFilter;
use Kibo\Phast\Filters\HTML\Helpers\JSDetectorTrait;
use Kibo\Phast\Filters\JavaScript\Minification\JSMinifierFilter;
use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;
use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\Services\Bundler\ServiceParams;
use Kibo\Phast\Services\Bundler\TokenRefMaker;
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
     * @var LocalRetriever
     */
    private $retriever;

    private $tokenRefMaker;

    /**
     * @var ObjectifiedFunctions
     */
    private $functions;

    /**
     * @var bool
     */
    private $didInject = false;

    public function __construct(
        array $config,
        ServiceSignature $signature,
        LocalRetriever $retriever,
        TokenRefMaker $tokenRefMaker,
        ObjectifiedFunctions $functions = null
    ) {
        $this->config = $config;
        $this->signature = $signature;
        $this->retriever = $retriever;
        $this->tokenRefMaker = $tokenRefMaker;
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
        $url = $this->getAbsoluteURL($src);
        $cacheMarker = $this->retriever->getCacheSalt($url);
        if (!$cacheMarker) {
            return;
        }
        $cacheMarker .= '-' . JSMinifierFilter::VERSION;
        $element->setAttribute('src', $this->makeProxiedURL($url, $cacheMarker));
        $element->setAttribute('data-phast-original-src', (string) $url);
        $element->setAttribute('data-phast-params', $this->makeServiceParams($url, $cacheMarker));
    }

    private function makeProxiedURL(URL $url, $cacheMarker) {
        $params = [
            'service' => 'scripts',
            'src' => (string) $url->withoutQuery(),
            'cacheMarker' => $cacheMarker,
        ];

        return (new ServiceRequest())
            ->withUrl(URL::fromString($this->config['serviceUrl']))
            ->withParams($params)
            ->serialize();
    }

    private function makeServiceParams(URL $url, $cacheMarker) {
        return ServiceParams::
            fromArray([
                'src' => (string) $url->withoutQuery(),
                'cacheMarker' => $cacheMarker,
                'isScript' => '1',
            ])
            ->sign($this->signature)
            ->replaceByTokenRef($this->tokenRefMaker)
            ->serialize();
    }

    private function addScript() {
        $config = [
            'serviceUrl' => $this->config['serviceUrl'],
            'pathInfo' => ServiceRequest::getDefaultSerializationMode() === ServiceRequest::FORMAT_PATH,
            'urlRefreshTime' => $this->config['urlRefreshTime'],
            'whitelist' => $this->config['match'],
        ];
        $script = PhastJavaScript::fromFile(__DIR__ . '/rewrite-function.js');
        $script->setConfig('script-proxy-service', $config);
        $this->context->addPhastJavaScript($script);
    }

    private function getAbsoluteURL($url) {
        return URL::fromString($url)->withBase($this->context->getBaseUrl());
    }
}
