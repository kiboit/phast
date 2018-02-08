<?php

namespace Kibo\Phast\Filters\HTML\CSSInlining;

use Kibo\Phast\Common\DOMDocument;
use Kibo\Phast\Filters\HTML\HTMLFilter;
use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\Services\ServiceFilter;
use Kibo\Phast\Services\ServiceRequest;
use Kibo\Phast\ValueObjects\PhastJavaScript;
use Kibo\Phast\ValueObjects\Resource;
use Kibo\Phast\ValueObjects\URL;

class Filter implements HTMLFilter {
    use LoggingTrait;

    const CSS_IMPORTS_REGEXP = '~
        @import \s++
        ( url \( )?+                # url() is optional
        ( (?(1) ["\']?+ | ["\'] ) ) # without url() a quote is necessary
        \s*+ (?<url>[A-Za-z0-9_/.:?&=+%,-]++) \s*+
        \2                          # match ending quote
        (?(1)\))                    # match closing paren if url( was used
        \s*+ ;
    ~xi';

    /**
     * @var ServiceSignature
     */
    private $signature;

    /**
     * @var bool
     */
    private $withIEFallback = false;

    /**
     * @var bool
     */
    private $hasDoneInlining = false;

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
     * @var int
     */
    private $optimizerSizeDiffThreshold;

    /**
     * @var Retriever
     */
    private $retriever;

    /**
     * @var OptimizerFactory
     */
    private $optimizerFactory;

    /**
     * @var ServiceFilter
     */
    private $cssFilter;

    /**
     * @var Optimizer
     */
    private $optimizer;

    public function __construct(
        ServiceSignature $signature,
        URL $baseURL,
        array $config,
        Retriever $retriever,
        OptimizerFactory $optimizerFactory,
        ServiceFilter $cssFilter
    ) {
        $this->signature = $signature;
        $this->baseURL = $baseURL;
        $this->serviceUrl = URL::fromString((string)$config['serviceUrl']);
        $this->urlRefreshTime = (int)$config['urlRefreshTime'];
        $this->optimizerSizeDiffThreshold = (int)$config['optimizerSizeDiffThreshold'];
        $this->retriever = $retriever;
        $this->optimizerFactory = $optimizerFactory;
        $this->cssFilter = $cssFilter;

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

    public function transformHTMLDOM(DOMDocument $document) {
        $this->optimizer = $this->optimizerFactory->makeForDocument($document);
        $links = iterator_to_array($document->query('//link'));
        $styles = iterator_to_array($document->query('//style'));
        foreach ($links as $link) {
            $this->inlineLink($link, $document->getBaseURL());
        }
        foreach ($styles as $style) {
            $this->inlineStyle($style);
        }
        if ($this->withIEFallback) {
            $this->addIEFallbackScript($document);
        }
        if ($this->hasDoneInlining) {
            $this->addInlinedRetrieverScript($document);
        }
    }

    private function inlineLink(\DOMElement $link, URL $baseUrl) {
        if (!$link->hasAttribute('rel')
            || $link->getAttribute('rel') != 'stylesheet'
            || !$link->hasAttribute('href')
        ) {
            return;
        }

        $location = URL::fromString(trim($link->getAttribute('href')))->withBase($baseUrl);

        if (!$this->findInWhitelist($location)) {
            return;
        }

        $media = $link->getAttribute('media');
        $elements = $this->inlineURL($link->ownerDocument, $location, $media);
        if (!is_null($elements)) {
            $this->replaceElement($elements, $link);
        }
    }

    private function inlineStyle(\DOMElement $style) {
        $processed = $this->cssFilter
            ->apply(Resource::makeWithContent($this->baseURL, $style->textContent), [])
            ->getContent();
        $elements = $this->inlineCSS(
            $style->ownerDocument,
            $this->baseURL,
            $processed,
            $style->getAttribute('media'),
            false
        );
        $this->replaceElement($elements, $style);
    }

    private function replaceElement($replacements, $element) {
        foreach ($replacements as $replacement) {
            $element->parentNode->insertBefore($replacement, $element);
        }
        $element->parentNode->removeChild($element);
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
     * @param DOMDocument $document
     * @param URL $url
     * @param string $media
     * @param boolean $ieCompatible
     * @param int $currentLevel
     * @param string[] $seen
     * @return \DOMElement[]
     */
    private function inlineURL(DOMDocument $document, URL $url, $media, $ieCompatible = true, $currentLevel = 0, $seen = []) {
        $whitelistEntry = $this->findInWhitelist($url);

        if (!$whitelistEntry) {
            $this->logger()->info('Not inlining {url}. Not in whitelist', ['url' => ($url)]);
            return [$this->makeLink($document, $url, $media)];
        }

        if (!$whitelistEntry['ieCompatible']) {
            $ieFallbackUrl = $ieCompatible ? $url : null;
            $ieCompatible = false;
        } else {
            $ieFallbackUrl = null;
        }

        if (in_array($url, $seen)) {
            return [];
        }

        if ($currentLevel > $this->maxInlineDepth) {
            return $this->addIEFallback($ieFallbackUrl, [$this->makeLink($document, $url, $media)]);
        }

        $seen[] = $url;

        $this->logger()->info('Inlining {url}.', ['url' => (string)$url]);
        $content = $this->retriever->retrieve($url);
        if ($content === false) {
            $this->logger()->error('Could not get contents for {url}', ['url' => (string)$url]);
            return $this->addIEFallback($ieFallbackUrl, [$this->makeServiceLink($document, $url, $media)]);
        }


        $content = $this->cssFilter->apply(Resource::makeWithContent($url, $content), [])
            ->getContent();
        $optimized = $this->optimizer->optimizeCSS($content);
        if (is_null($optimized)) {
            return null;
        }
        $isOptimized = false;
        if (strlen($content) - strlen($optimized) > $this->optimizerSizeDiffThreshold) {
            $content = $optimized;
            $isOptimized = true;
        }
        $this->hasDoneInlining = true;
        $elements = $this->inlineCSS(
            $document,
            $url,
            $content,
            $media,
            $isOptimized,
            $ieCompatible,
            $currentLevel,
            $seen
        );
        $this->addIEFallback($ieFallbackUrl, $elements);
        return $elements;
    }

    private function inlineCSS(
        DOMDocument $document,
        URL $url,
        $content,
        $media,
        $optimized,
        $ieCompatible = true,
        $currentLevel = 0,
        $seen = []
    ) {

        $urlMatches = $this->getImportedURLs($content);
        $elements = [];
        foreach ($urlMatches as $match) {
            $content = str_replace($match[0], '', $content);
            $matchedUrl = URL::fromString($match['url'])->withBase($url);
            $replacement = $this->inlineURL($document, $matchedUrl, $media, $ieCompatible, $currentLevel + 1, $seen);
            $elements = array_merge($elements, $replacement);
        }

        $elements[] = $this->makeStyle($document, $url, $content, $media, $optimized);

        return $elements;
    }

    private function makeServiceLink(DOMDocument $document, URL $location, $media) {
        $url = $this->makeServiceURL($location);
        return $this->makeLink($document, URL::fromString($url), $media);
    }

    private function addIEFallback(URL $fallbackUrl = null, array $elements = null) {
        if ($fallbackUrl === null || !$elements) {
            return $elements;
        }

        foreach ($elements as $element) {
            $element->setAttribute('data-phast-nested-inlined', '');
        }

        $element->setAttribute('data-phast-ie-fallback-url', (string)$fallbackUrl);
        $element->removeAttribute('data-phast-nested-inlined');

        $this->logger()->info('Set {url} as IE fallback URL', ['url' => (string)$fallbackUrl]);

        $this->withIEFallback = true;

        return $elements;
    }

    private function addIEFallbackScript(DOMDocument $document) {
        $this->logger()->info('Adding IE fallback script');
        $this->withIEFallback = false;
        $document->addPhastJavaScript(new PhastJavaScript(__DIR__ . '/ie-fallback.js'));
    }

    private function addInlinedRetrieverScript(DOMDocument $document) {
        $this->logger()->info('Adding inlined retriever script');
        $this->hasDoneInlining = false;
        $document->addPhastJavaScript(new PhastJavaScript(__DIR__ . '/inlined-css-retriever.js'));
    }

    private function getImportedURLs($cssContent) {
        preg_match_all(self::CSS_IMPORTS_REGEXP,
            $cssContent,
            $matches,
            PREG_SET_ORDER
        );
        return $matches;
    }

    private function makeStyle(DOMDocument $document, URL $url, $content, $media, $optimized) {
        $style = $document->createElement('style');
        if ($media !== '') {
            $style->setAttribute('media', $media);
        }
        if ($optimized) {
            $style->setAttribute('data-phast-href', $this->makeServiceURL($url, true));
        }
        $style->textContent = $content;
        return $style;
    }

    private function makeLink(DOMDocument $document, URL $url, $media) {
        $link = $document->createElement('link');
        $link->setAttribute('rel', 'stylesheet');
        $link->setAttribute('href', (string)$url);
        if ($media !== '') {
            $link->setAttribute('media', $media);
        }
        return $link;
    }

    protected function makeServiceURL(URL $originalLocation, $stripImports = false) {
        $lastModTime = $this->retriever->getLastModificationTime($originalLocation);
        $params = [
            'src' => (string) $originalLocation,
            'cacheMarker' => $lastModTime ? $lastModTime : floor(time() / $this->urlRefreshTime)
        ];
        if ($stripImports) {
            $params['strip-imports'] = 1;
        }
        return (new ServiceRequest())->withParams($params)
            ->withUrl($this->serviceUrl)
            ->sign($this->signature)
            ->serialize();
    }
}
