<?php

namespace Kibo\Phast\Filters\HTML;

use Kibo\Phast\Filters\HTML\Helpers\BodyFinderTrait;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\ValueObjects\URL;

class CSSInliningHTMLFilter implements HTMLFilter {
    use BodyFinderTrait;

    /**
     * @var string
     */
    private $ieFallbackScript = <<<EOJS
(function() {
    
    var ua = window.navigator.userAgent;
    if (ua.indexOf('MSIE ') === -1 && ua.indexOf('Trident/') === -1) {
        return;
    }
    
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
           
            var group = el.getAttribute('data-phast-ie-fallback-group');
            Array.prototype.forEach.call(
                document.querySelectorAll('style[data-phast-ie-fallback-group="' + group + '"]'),
                function (groupEl) {
                    groupEl.parentNode.removeChild(groupEl);
                }
            );
        }
    );
    
})();
EOJS;


    /**
     * @var bool
     */
    private $ieFallbackGroup = 0;

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

    public function __construct(URL $baseURL, array $config, Retriever $retriever) {
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
        if ($this->ieFallbackGroup) {
            $this->ieFallbackGroup = 0;
            $script = $document->createElement('script');
            $script->textContent = $this->ieFallbackScript;
            $this->getBodyElement($document)->appendChild($script);
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
            $this->ieFallbackGroup++;
        }

        $seen = [(string)$location];
        $elements = $this->inlineURL($document, $location, 0, $seen);
        if (empty ($elements)) {
            $this->redirectLinkToService($link);
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

    private function redirectLinkToService(\DOMElement $link) {
        $params = [
            'src' => $link->getAttribute('href'),
            'cacheMarker' => floor(time() / $this->urlRefreshTime)
        ];
        $glue = strpos($this->serviceUrl, '?') !== false ? '&' : '?';
        $link->setAttribute('href', $this->serviceUrl . $glue . http_build_query($params));
    }

    private function transformLinkToElements(\DOMElement $link, array $elements, $ieCompatible) {
        $media = (string)$link->getAttribute('media');
        foreach ($elements as $element) {
            if ($media) {
                $element->setAttribute('media', $media);
            }
            if (!$ieCompatible) {
                $element->setAttribute('data-phast-ie-fallback-group', $this->ieFallbackGroup);
            }
            $link->parentNode->insertBefore($element, $link);
        }
        if (!$ieCompatible) {
            $element->setAttribute('data-phast-ie-fallback-url', $link->getAttribute('href'));
        }
        $link->parentNode->removeChild($link);
    }

    private function getImportedURLs($cssContent) {
        preg_match_all(
            '~
                @import \s+
                (?: url \s* \( )?
                (?:"|\'|)
                (?<url>[A-Za-z0-9_/.:-]+)
                (?:"|\'|) (?: \) )?
                \s*
                ;
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
                    @import \s* (?:"|\'|)
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
