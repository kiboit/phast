<?php

namespace Kibo\Phast\Filters\HTML;

use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Filters\HTML\Helpers\JSDetectorTrait;
use Kibo\Phast\Filters\HTML\Helpers\SignedUrlMakerTrait;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\ValueObjects\URL;

class ScriptProxyServiceHTMLFilter implements HTMLFilter {
    use JSDetectorTrait, SignedUrlMakerTrait;

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
            if (!$this->isJSElement($script)) {
                continue;
            }
            $this->rewriteScriptSource($script);
            $this->rewriteScriptBody($script);
        }
    }

    private function rewriteScriptSource(\DOMElement $element) {
        if (!$element->hasAttribute('src')) {
            return;
        }
        $element->setAttribute('src', $this->rewriteURL($element->getAttribute('src')));
    }

    private function rewriteScriptBody(\DOMElement $element) {
        $pattern = '~
            ( [\'"] )
            (
                (?: http s? : ) ?
                // (?: [a-z0-9] [a-z0-9-]* \. )* [a-z]+ /
                [a-z0-9_./?&=-]*
            )
            ( \1 )
        ~x';

        $element->textContent = preg_replace_callback($pattern, function ($match) {
            return $match[1] . $this->rewriteURL($match[2]) . $match[3];
        }, $element->textContent);
    }

    private function rewriteURL($src) {
        $url = URL::fromString($src)->withBase($this->baseUrl);
        if (!$this->shouldRewriteURL($url)) {
            return $url;
        }
        $params = [
            'src' => (string) $url,
            'cacheMarker' => floor($this->functions->time() / $this->config['urlRefreshTime'])
        ];
        return $this->makeSignedUrl($this->config['serviceUrl'], $params, $this->signature);
    }

    private function shouldRewriteURL(URL $url) {
        if ($url->isLocalTo($this->baseUrl)) {
            return false;
        }
        $str = (string) $url;
        foreach ($this->config['match'] as $pattern) {
            if (preg_match($pattern, $str)) {
                return true;
            }
        }
        return false;
    }

}
