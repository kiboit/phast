<?php

namespace Kibo\Phast\Filters\HTML;

use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Filters\HTML\Helpers\JSDetectorTrait;
use Kibo\Phast\Filters\HTML\Helpers\SignedUrlMakerTrait;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\ValueObjects\URL;

class ScriptProxyServiceHTMLFilter implements HTMLFilter {
    use JSDetectorTrait, SignedUrlMakerTrait;

    private $rewriteFunction = <<<EOS
(function(opt) {
    var url_pattern = /^(https?:)?\/\//;

    overrideDOMMethod('appendChild');
    overrideDOMMethod('insertBefore');

    function overrideDOMMethod(name) {
        var original = Element.prototype[name];
        Element.prototype[name] = function () {
            processNode(arguments[0]);
            return original.apply(this, arguments);
        };
    }

    function processNode(el) {
        if (!el) {
            return;
        }
        if (el.nodeType !== Node.ELEMENT_NODE) {
            return;
        }
        if (el.tagName !== 'SCRIPT') {
            return;
        }
        if (!url_pattern.test(el.src)) {
            return;
        }
        el.src = opt.serviceUrl + '&src=' + escape(el.src);
    }
})
EOS;

    /**
     * @var URL
     */
    private $baseUrl;

    /**
     * @var array
     */
    private $config;

    /**
     * @var ServiceSignature
     */
    private $signature;

    /**
     * @var ObjectifiedFunctions
     */
    private $functions;

    /**
     * ScriptProxyServiceHTMLFilter constructor.
     *
     * @param URL $baseUrl
     * @param array $config
     * @param ServiceSignature $signature
     * @param ObjectifiedFunctions|null $functions
     */
    public function __construct(
        URL $baseUrl,
        array $config,
        ServiceSignature $signature,
        ObjectifiedFunctions $functions = null
    ) {
        $this->baseUrl = $baseUrl;
        $this->config = $config;
        $this->signature = $signature;
        $this->functions = is_null($functions) ? new ObjectifiedFunctions() : $functions;
    }

    public function transformHTMLDOM(\DOMDocument $document) {
        $scripts = iterator_to_array($document->getElementsByTagName('script'));
        $didInject = false;
        foreach ($scripts as $script) {
            if (!$this->isJSElement($script)) {
                continue;
            }
            $this->rewriteScriptSource($script);
            if (!$didInject) {
                $this->injectScriptBefore($script);
                $didInject = true;
            }
        }
    }

    private function rewriteScriptSource(\DOMElement $element) {
        if (!$element->hasAttribute('src')) {
            return;
        }
        $element->setAttribute('src', $this->rewriteURL($element->getAttribute('src')));
    }

    private function rewriteURL($src) {
        $url = URL::fromString($src)->withBase($this->baseUrl);
        if (!$this->shouldRewriteURL($url)) {
            return $src;
        }
        $params = [
            'src' => (string) $url,
            'cacheMarker' => floor($this->functions->time() / $this->config['urlRefreshTime'])
        ];
        return $this->makeSignedUrl($this->config['serviceUrl'], $params, $this->signature);
    }

    private function shouldRewriteURL(URL $url) {
        if ($url->isLocalTo($this->baseUrl)) {
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

    private function injectScriptBefore($beforeScript) {
        $options = [
            'serviceUrl' => $this->config['serviceUrl'],
        ];
        $script = $beforeScript->ownerDocument->createElement('script');
        $script->textContent = $this->rewriteFunction . '(' . json_encode($options) . ')';
        $beforeScript->parentNode->insertBefore($script, $beforeScript);
    }

}
