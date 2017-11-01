<?php

namespace Kibo\Phast\Filters\HTML;

use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Filters\HTML\Helpers\JSDetectorTrait;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\ValueObjects\URL;

class ScriptProxyServiceHTMLFilter implements HTMLFilter {
    use JSDetectorTrait;

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
        $scripts = $document->getElementsByTagName('script');
        foreach ($scripts as $script) {
            if ($this->shouldRewrite($script)) {
                $this->rewrite($script);
            }
        }
    }

    private function shouldRewrite(\DOMElement $element) {
        if (!$this->isJSElement($element)) {
            return false;
        }
        if (!$element->hasAttribute('src')) {
            return false;
        }
        $src = $element->getAttribute('src');
        if (URL::fromString($src)->isLocalTo($this->baseUrl)) {
            return false;
        }
        foreach ($this->config['match'] as $pattern) {
            if (preg_match($pattern, $src)) {
                return true;
            }
        }
        return false;
    }

    private function rewrite(\DOMElement $element) {
        $params = [
            'src' => $element->getAttribute('src'),
            'cacheMarker' => floor($this->functions->time() / $this->config['urlRefreshTime'])
        ];
        $query = http_build_query($params);
        $newSrc = $this->config['serviceUrl'] . '?' . $query . '&token=' . $this->signature->sign($query);
        $element->setAttribute('src', $newSrc);
    }

}
