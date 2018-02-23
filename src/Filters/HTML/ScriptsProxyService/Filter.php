<?php

namespace Kibo\Phast\Filters\HTML\ScriptsProxyService;

use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Filters\HTML\BaseHTMLStreamFilter;
use Kibo\Phast\Filters\HTML\Helpers\JSDetectorTrait;
use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;
use Kibo\Phast\Retrievers\Retriever;
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
     * @var Retriever
     */
    private $retriever;

    /**
     * @var ObjectifiedFunctions
     */
    private $functions;

    private $ids = [];

    /**
     * @var bool
     */
    private $didInject = false;

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
        $id = $this->getRewriteId($src);
        $url = $this->rewriteURL($src, $id);
        $element->setAttribute('src', $url);
        $element->setAttribute('data-phast-proxied-script', $id);
    }

    private function getRewriteId($src) {
        $id = "s" . md5($src);
        if (isset ($this->ids[$id])) {
            $id .= '-' . $this->ids[$id]++;
        } else {
            $this->ids[$id] = 1;
        }
        return $id;
    }

    private function rewriteURL($src, $id) {
        $url = URL::fromString($src)->withBase($this->context->getBaseUrl());
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

}
