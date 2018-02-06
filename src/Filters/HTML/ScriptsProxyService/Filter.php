<?php

namespace Kibo\Phast\Filters\HTML\ScriptsProxyService;

use Kibo\Phast\Common\DOMDocument;
use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Filters\HTML\Helpers\JSDetectorTrait;
use Kibo\Phast\Filters\HTML\HTMLFilter;
use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\Services\ServiceRequest;
use Kibo\Phast\ValueObjects\URL;

class Filter implements HTMLFilter {
    use JSDetectorTrait, LoggingTrait;

    /**
     * @var URL
     */
    private $baseUrl;

    /**
     * @var array
     */
    private $config;

    /**
     * @var Retriever
     */
    private $retriever;

    /**
     * @var ObjectifiedFunctions
     */
    private $functions;

    private $id = 0;

    /**
     * Filter constructor.
     * @param array $config
     * @param Retriever $retriever
     * @param ObjectifiedFunctions|null $functions
     */
    public function __construct(
        array $config,
        Retriever $retriever,
        ObjectifiedFunctions $functions = null
    ) {
        $this->config = $config;
        $this->retriever = $retriever;
        $this->functions = is_null($functions) ? new ObjectifiedFunctions() : $functions;
    }

    public function transformHTMLDOM(DOMDocument $document) {
        $this->baseUrl = $document->getBaseURL();
        $scripts = iterator_to_array($document->query('//script'));
        $didInject = false;
        foreach ($scripts as $script) {
            if (!$this->isJSElement($script)) {
                continue;
            }
            $this->rewriteScriptSource($script);
            if (!$didInject) {
                $this->addScript($document);
                $didInject = true;
            }
        }
    }

    private function rewriteScriptSource(\DOMElement $element) {
        if (!$element->hasAttribute('src')) {
            return;
        }
        $id = "s" . ++$this->id;
        $url = $this->rewriteURL(trim($element->getAttribute('src')), $id);
        $element->setAttribute('src', $url);
        $element->setAttribute('data-phast-proxied-script', $id);
    }

    private function rewriteURL($src, $id) {
        $url = URL::fromString($src)->withBase($this->baseUrl);
        if (!$this->shouldRewriteURL($url)) {
            $this->logger()->info('Not proxying {src}', ['src' => $src]);
            return $src;
        }
        $this->logger()->info('Proxying {src}', ['src' => $src]);
        $lastModTime = $this->retriever->getLastModificationTime($url);
        $params = [
            'src' => (string) $url,
            'id' => $id,
            'cacheMarker' => $lastModTime
                             ? $lastModTime
                             : floor($this->functions->time() / $this->config['urlRefreshTime'])
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

    private function addScript(DOMDocument $document) {
        $config = [
            'serviceUrl' => $this->config['serviceUrl'],
            'urlRefreshTime' => $this->config['urlRefreshTime'],
            'whitelist' => $this->config['match']
        ];
        $script = new RewriteFunctionPhastJavaScript(__DIR__ . '/rewrite-function.js');
        $script->setConfig($config);
        $document->addPhastJavaScript($script);
    }

}
