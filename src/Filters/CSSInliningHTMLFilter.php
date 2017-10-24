<?php

namespace Kibo\Phast\Filters;

use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\ValueObjects\URL;

class CSSInliningHTMLFilter implements HTMLFilter {

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
                && !$this->isCrossSiteUrl(URL::fromString($link->getAttribute('href')));
    }

    private function inline(\DOMElement $link, \DOMDocument $document) {
        $location = URL::fromString($link->getAttribute('href'))->withBase($this->baseURL);
        $content = $this->retriever->retrieve($location);
        if ($content === false) {
            return;
        }
        $style = $document->createElement('style');
        $style->textContent = $this->rewriteRelativeURLs($content, $location);
        if ($link->hasAttribute('media')) {
            $style->setAttribute('media', $link->getAttribute('media'));
        }
        $link->parentNode->insertBefore($style, $link);
        $link->parentNode->removeChild($link);
    }

    /**
     * @param URL $url
     * @return bool
     */
    private function isCrossSiteUrl(URL $url) {
        return !empty ($url->getHost()) && $url->getHost() != $this->baseURL->getHost();
    }

    private function rewriteRelativeURLs($cssContent, URL $cssUrl) {
        return preg_replace_callback(
            '~
                (
                    @import \s+ ["\'] |
                    url\( \s* (?:"|\'|)
                )
                (?! (?:[a-z]+:)? // )
                ([A-Za-z0-9_/.-])
            ~xi',
            function ($match) use ($cssUrl) {
                return $match[1] . URL::fromString($match[2])->withBase($cssUrl);
            },
            $cssContent
        );
    }
}
