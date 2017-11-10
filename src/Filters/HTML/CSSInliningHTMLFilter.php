<?php

namespace Kibo\Phast\Filters\HTML;

use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\ValueObjects\URL;

class CSSInliningHTMLFilter implements HTMLFilter {

    /**
     * @var int
     */
    private $maxInlineDepth = 2;

    /**
     * @var URL
     */
    private $baseURL;

    /**
     * @var Retriever
     */
    private $retriever;

    public function __construct(URL $baseURL, Retriever $retriever) {
        $this->baseURL = $baseURL;
        $this->retriever = $retriever;
    }

    public function transformHTMLDOM(\DOMDocument $document) {
        $links = iterator_to_array($document->getElementsByTagName('link'));
        foreach ($links as $link) {
            if ($this->shouldInline($link)) {
                $this->inline($link, $document);
            }
        }
    }

    private function shouldInline(\DOMElement $link) {
        return  $link->hasAttribute('rel')
                && $link->getAttribute('rel') == 'stylesheet'
                && $link->hasAttribute('href')
                && URL::fromString($link->getAttribute('href'))->isLocalTo($this->baseURL);
    }

    private function inline(\DOMElement $link, \DOMDocument $document) {
        $location = URL::fromString($link->getAttribute('href'))->withBase($this->baseURL);
        $seen = [(string)$location];
        $elements = $this->inlineURL($document, $location, 0, $seen);
        if (empty ($elements)) {
            return;
        }
        $media = (string)$link->getAttribute('media');
        foreach ($elements as $element) {
            if ($media) {
                $element->setAttribute('media', $media);
            }
            $link->parentNode->insertBefore($element, $link);
        }
        $link->parentNode->removeChild($link);
    }

    /**
     * @param \DOMDocument $document
     * @param URL $url
     * @param string $media
     * @param int $currentLevel
     * @param string[] $seen
     * @return \DOMElement[]
     */
    private function inlineURL(\DOMDocument $document, URL $url, $currentLevel, &$seen) {
        $content = $this->retriever->retrieve($url);
        if ($content === false) {
            return $currentLevel > 0 ? [$this->makeLink($document, $url)] : [];
        }

        $content = $this->minify($content);
        $urlMatches = $this->getImportedURLs($content);
        $elements = [];
        foreach ($urlMatches as $match) {
            $content = str_replace($match[0], '', $content);
            $url = URL::fromString($match['url'])->withBase($url);
            if (in_array((string)$url, $seen)) {
                continue;
            }
            $seen[] = (string)$url;

            if ($currentLevel == $this->maxInlineDepth) {
                $elements[] = $this->makeLink($document, $url);
            } else {
                $elements = array_merge(
                    $elements,
                    $this->inlineURL($document, $url, $currentLevel + 1, $seen)
                );
            }
        }

        $content = $this->rewriteRelativeURLs($content, $url);
        $elements[] = $this->makeStyle($document, $content);

        return $elements;
    }

    private function getImportedURLs($cssContent) {
        preg_match_all(
            '~
                @import \s+
                (?: url \s* \( )?
                (?:"|\'|)
                (?<url>[A-Za-z0-9_/.-]+)
                (?:"|\'|) (?: \) )?
                \s*
                (?<media>[^;]*)
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
