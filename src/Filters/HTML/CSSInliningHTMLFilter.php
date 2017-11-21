<?php

namespace Kibo\Phast\Filters\HTML;

use Kibo\Phast\Filters\HTML\Helpers\BodyFinderTrait;
use Kibo\Phast\Filters\HTML\Helpers\SignedUrlMakerTrait;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\ValueObjects\URL;

class CSSInliningHTMLFilter implements HTMLFilter {
    use BodyFinderTrait, SignedUrlMakerTrait;

    /**
     * @var string
     */
    private $ieFallbackScript = <<<EOJS
(function() {
    
    var ua = window.navigator.userAgent;
    if (ua.indexOf('MSIE ') === -1 && ua.indexOf('Trident/') === -1) {
        return;
    }
    
    document.addEventListener('readystatechange', function () {
        Array.prototype.forEach.call(
            document.querySelectorAll('link[data-phast-ie-fallback-url]'),
            function (el) {
                console.log(el);
                el.getAttribute('data-phast-ie-fallback-url');
                el.setAttribute('href', el.getAttribute('data-phast-ie-fallback-url'));
            }
        );
    });

    
    Array.prototype.forEach.call(
        document.querySelectorAll('style[data-phast-ie-fallback-url]'),
        function (el) {
            var link = document.createElement('link');
            if (el.hasAttribute('media')) {
                link.setAttribute('media', el.getAttribute('media'));
            }
            link.setAttribute('rel', 'stylesheet');
            link.setAttribute('href', el.getAttribute('data-phast-ie-fallback-url'));
            el.parentNode.insertBefore(link, el);
            el.parentNode.removeChild(el);
        }
    );
    Array.prototype.forEach.call(
        document.querySelectorAll('style[data-phast-nested-inlined]'),
        function (groupEl) {
            groupEl.parentNode.removeChild(groupEl);
        }
    );
    
})();
EOJS;

    /**
     * @var ServiceSignature
     */
    private $signature;

    /**
     * @var bool
     */
    private $withIEFallback = false;

    /**
     * @var int
     */
    private $maxInlineDepth = 2;

    /**
     * @var URL
     */
    private $baseURL;

    /**
     * @var string[]
     */
    private $whitelist = [];

    /**
     * @var string
     */
    private $serviceUrl;

    /**
     * @var int
     */
    private $urlRefreshTime;


    /**
     * @var Retriever
     */
    private $retriever;

    public function __construct(ServiceSignature $signature, URL $baseURL, array $config, Retriever $retriever) {
        $this->signature = $signature;
        $this->baseURL = $baseURL;
        $this->serviceUrl = (string)$config['serviceUrl'];
        $this->urlRefreshTime = (int)$config['urlRefreshTime'];
        $this->retriever = $retriever;

        foreach ($config['whitelist'] as $key => $value) {
            if (!is_array($value)) {
                $this->whitelist[$value] = ['ieCompatible' => true];
                $key = $value;
            } else {
                $this->whitelist[$key] = $value;
            }
            if (!isset ($this->whitelist[$key]['ieCompatible'])) {
                $this->whitelist[$key] = true;
            }
        }
    }

    public function transformHTMLDOM(\DOMDocument $document) {
        $links = iterator_to_array($document->getElementsByTagName('link'));
        foreach ($links as $link) {
            if ($this->shouldInline($link)) {
                $this->inline($link, $document);
            }
        }
        if ($this->withIEFallback) {
            $this->addIEFallbackScript($document);
        }
    }

    private function shouldInline(\DOMElement $link) {
        return  $link->hasAttribute('rel')
                && $link->getAttribute('rel') == 'stylesheet'
                && $link->hasAttribute('href');
    }

    private function inline(\DOMElement $link, \DOMDocument $document) {
        $location = URL::fromString($link->getAttribute('href'))->withBase($this->baseURL);
        $whitelistEntry = $this->findInWhitelist($location);
        if (!$whitelistEntry) {
            return;
        }
        if (!$whitelistEntry['ieCompatible']) {
            $this->withIEFallback = true;
        }

        $seen = [(string)$location];
        $elements = $this->inlineURL($document, $location, 0, $seen);
        if (empty ($elements)) {
            $this->redirectLinkToService($link, $whitelistEntry['ieCompatible']);
            return;
        } else {
            $this->transformLinkToElements($link, $elements, $whitelistEntry['ieCompatible']);
        }

    }

    private function findInWhitelist(URL $url) {
        $stringUrl = (string)$url;
        foreach ($this->whitelist as $pattern => $settings) {
            if (preg_match($pattern, $stringUrl)) {
                return $settings;
            }
        }
        return false;
    }

    /**
     * @param \DOMDocument $document
     * @param URL $url
     * @param int $currentLevel
     * @param string[] $seen
     * @return \DOMElement[]
     */
    private function inlineURL(\DOMDocument $document, URL $url, $currentLevel, &$seen) {
        $content = $this->retriever->retrieve($url);
        if ($content === false) {
            return [];
        }
        $content = $this->minify($content);
        $urlMatches = $this->getImportedURLs($content);
        $elements = [];
        foreach ($urlMatches as $match) {
            $matchedUrl = URL::fromString($match['url'])->withBase($url);
            if (!$this->findInWhitelist($matchedUrl)) {
                continue;
            }
            $content = str_replace($match[0], '', $content);
            if (in_array((string)$matchedUrl, $seen)) {
                continue;
            }
            $seen[] = (string)$matchedUrl;

            if ($currentLevel == $this->maxInlineDepth) {
                $elements[] = $this->makeLink($document, $matchedUrl);
            } else {
                $elements = array_merge(
                    $elements,
                    $this->inlineURL($document, $matchedUrl, $currentLevel + 1, $seen)
                );
            }
        }

        $content = $this->rewriteRelativeURLs($content, $url);
        $elements[] = $this->makeStyle($document, $content);

        return $elements;
    }

    private function redirectLinkToService(\DOMElement $link, $ieCompatible) {
        $location = URL::fromString($link->getAttribute('href'))->withBase($this->baseURL);
        $params = [
            'src' => (string) $location,
            'cacheMarker' => floor(time() / $this->urlRefreshTime)
        ];
        $url = $this->makeSignedUrl($this->serviceUrl, $params, $this->signature);
        $link->setAttribute('href', $url);
        if (!$ieCompatible) {
            $link->setAttribute('data-phast-ie-fallback-url', $location);
        }
        $this->withIEFallback++;
    }

    private function transformLinkToElements(\DOMElement $link, array $elements, $ieCompatible) {
        $media = (string)$link->getAttribute('media');
        foreach ($elements as $element) {
            if ($media) {
                $element->setAttribute('media', $media);
            }
            if (!$ieCompatible) {
                $element->setAttribute('data-phast-nested-inlined', '');
            }
            $link->parentNode->insertBefore($element, $link);
        }
        if (!$ieCompatible) {
            $element->setAttribute('data-phast-ie-fallback-url', $link->getAttribute('href'));
            $element->removeAttribute('data-phast-nested-inlined');
        }
        $link->parentNode->removeChild($link);
    }

    private function addIEFallbackScript(\DOMDocument $document) {
        $this->withIEFallback = false;
        $script = $document->createElement('script');
        $script->setAttribute('data-phast-no-defer', 'data-phast-no-defer');
        $script->textContent = $this->ieFallbackScript;
        $this->getBodyElement($document)->appendChild($script);
    }

    private function getImportedURLs($cssContent) {
        preg_match_all(
            '~
                @import \s+
                ( url \( )?                 # url() is optional
                ( (?(1) ["\']? | ["\'] ) )  # without url() a quote is necessary
                (?<url>[A-Za-z0-9_/.:-]+)
                \2                          # match ending quote
                (?(1)\))                    # match closing paren if url( was used
                \s* ;
            ~xi',
            $cssContent,
            $matches,
            PREG_SET_ORDER
        );
        return $matches;
    }

    private function makeStyle(\DOMDocument $document, $content) {
        $style = $document->createElement('style');
        $style->textContent = $content;
        return $style;
    }

    private function makeLink(\DOMDocument $document, URL $url) {
        $link = $document->createElement('link');
        $link->setAttribute('rel', 'stylesheet');
        $link->setAttribute('href', (string)$url);
        return $link;
    }

    private function minify($content) {
        // Remove comments
        $content = preg_replace('~/\*[^*]*\*+([^/*][^*]*\*+)*/~', '', $content);
        // Remove extraneous whitespace (not before colons)
        $content = preg_replace('~([,{}:;])\s+~', '$1', $content);
        $content = preg_replace('~\s+([,{};])~', '$1', $content);
        return trim($content);
    }

    private function rewriteRelativeURLs($cssContent, URL $cssUrl) {
        return preg_replace_callback(
            '~
                (
                    @import \s+ (?: url\( \s* )?+ (?:"|\'|)
                    |
                    url\( \s* (?:"|\'|)
                )
                (?! [a-z]+: | // )
                ([A-Za-z0-9_/.-])
            ~xi',
            function ($match) use ($cssUrl) {
                return $match[1] . URL::fromString($match[2])->withBase($cssUrl);
            },
            $cssContent
        );
    }
}
