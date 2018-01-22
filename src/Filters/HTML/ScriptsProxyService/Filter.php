<?php

namespace Kibo\Phast\Filters\HTML\ScriptsProxyService;

use Kibo\Phast\Common\DOMDocument;
use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Filters\HTML\Helpers\JSDetectorTrait;
use Kibo\Phast\Filters\HTML\HTMLFilter;
use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\Services\ServiceRequest;
use Kibo\Phast\ValueObjects\URL;

class Filter implements HTMLFilter {
    use JSDetectorTrait, LoggingTrait;

    private $rewriteFunction = <<<EOS
(function(config) {
    var urlPattern = /^(https?:)?\/\//;
    var cacheMarker = Math.floor((new Date).getTime() / 1000 / config.urlRefreshTime);

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
        if (!urlPattern.test(el.src)) {
            return;
        }
        el.src = config.serviceUrl + '&src=' + escape(el.src) +
                                     '&cacheMarker=' + escape(cacheMarker);
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
     * @var ObjectifiedFunctions
     */
    private $functions;

    /**
     * ScriptProxyServiceHTMLFilter constructor.
     *
     * @param URL $baseUrl
     * @param array $config
     * @param ObjectifiedFunctions|null $functions
     */
    public function __construct(
        URL $baseUrl,
        array $config,
        ObjectifiedFunctions $functions = null
    ) {
        $this->baseUrl = $baseUrl;
        $this->config = $config;
        $this->functions = is_null($functions) ? new ObjectifiedFunctions() : $functions;
    }

    public function transformHTMLDOM(DOMDocument $document) {
        $scripts = iterator_to_array($document->query('//script'));
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
            $this->logger()->info('Not proxying {src}', ['src' => $src]);
            return $src;
        }
        $this->logger()->info('Proxying {src}', ['src' => $src]);
        $params = [
            'src' => (string) $url,
            'cacheMarker' => floor($this->functions->time() / $this->config['urlRefreshTime'])
        ];
        return (new ServiceRequest())
            ->withUrl(URL::fromString($this->config['serviceUrl']))
            ->withParams($params)
            ->serialize();
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
        $config = [
            'serviceUrl' => $this->config['serviceUrl'],
            'urlRefreshTime' => $this->config['urlRefreshTime']
        ];
        $script = $beforeScript->ownerDocument->createElement('script');
        $script->textContent = $this->rewriteFunction . '(' . json_encode($config) . ')';
        $beforeScript->parentNode->insertBefore($script, $beforeScript);
    }

}
