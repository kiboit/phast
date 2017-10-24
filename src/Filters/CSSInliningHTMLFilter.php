<?php

namespace Kibo\Phast\Filters;

class CSSInliningHTMLFilter implements HTMLFilter {

    /**
     * @var string
     */
    private $baseURL;

    /**
     * @var callable
     */
    private $retrieveFile;

    public function __construct($baseURL, callable $fileRetrieverCallback) {
        $this->baseURL = $baseURL;
        $this->retrieveFile = $fileRetrieverCallback;
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
                && !$this->isCrossSiteUrl($link->getAttribute('href'));
    }

    private function inline(\DOMElement $link, \DOMDocument $document) {
        $location = $link->getAttribute('href');
        $content = call_user_func($this->retrieveFile, $location);
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
     * @param string $url
     * @return bool
     */
    private function isCrossSiteUrl($url) {
        $urlComponents = parse_url($url);
        return !empty ($urlComponents['host']) && $urlComponents['host'] != $this->baseURL;
    }

    private function rewriteRelativeURLs($cssContent, $cssUrl) {
        $cssUrlComponents = parse_url($cssUrl);
        $absBase = $this->compileUrl(array_merge($cssUrlComponents, [
            'path' => '/',
            'query' => null,
            'fragment' => null,
        ]));

        $relBase = $this->compileUrl(array_merge($cssUrlComponents, [
            'path' => preg_replace('~[^/]*$~', '', $cssUrlComponents['path']),
            'query' => null,
            'fragment' => null,
        ]));

        return preg_replace_callback(
            '~
                (
                    @import \s+ ["\'] |
                    url\( \s* (?:"|\'|)
                )
                (?! (?:[a-z]+:)? // )
                ([A-Za-z0-9_/.-])
            ~xi',
            function ($match) use ($absBase, $relBase) {
                $base = $match[2] == '/' ? $absBase : $relBase;
                return $match[1] . $base . $match[2];
            },
            $cssContent
        );
    }

    private function compileUrl(array $urlComponents) {
        $scheme   = isset($urlComponents['scheme']) ? $urlComponents['scheme'] . '://' : '';
        $host     = isset($urlComponents['host']) ? $urlComponents['host'] : '';
        $port     = isset($urlComponents['port']) ? ':' . $urlComponents['port'] : '';
        $user     = isset($urlComponents['user']) ? $urlComponents['user'] : '';
        $pass     = isset($urlComponents['pass']) ? ':' . $urlComponents['pass']  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = isset($urlComponents['path']) ? $urlComponents['path'] : '';
        $query    = isset($urlComponents['query']) ? '?' . $urlComponents['query'] : '';
        $fragment = isset($urlComponents['fragment']) ? '#' . $urlComponents['fragment'] : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }



}
