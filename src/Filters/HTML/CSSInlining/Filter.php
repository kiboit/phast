<?php

namespace Kibo\Phast\Filters\HTML\CSSInlining;

use Kibo\Phast\Common\Base64url;
use Kibo\Phast\Filters\HTML\BaseHTMLStreamFilter;
use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\Services\Bundler\ServiceParams;
use Kibo\Phast\Services\Bundler\TokenRefMaker;
use Kibo\Phast\Services\ServiceFilter;
use Kibo\Phast\Services\ServiceRequest;
use Kibo\Phast\ValueObjects\PhastJavaScript;
use Kibo\Phast\ValueObjects\Resource;
use Kibo\Phast\ValueObjects\URL;

class Filter extends BaseHTMLStreamFilter {
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
    private $optimizerSizeDiffThreshold;

    /**
     * @var Retriever
     */
    private $localRetriever;

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

    /**
     * @var TokenRefMaker
     */
    private $tokenRefMaker;

    /**
     * @var string[]
     */
    private $cacheMarkers = [];

    /**
     * @var string
     */
    private $cspNonce;

    public function __construct(
        ServiceSignature $signature,
        URL $baseURL,
        array $config,
        Retriever $localRetriever,
        Retriever $retriever,
        OptimizerFactory $optimizerFactory,
        ServiceFilter $cssFilter,
        TokenRefMaker $tokenRefMaker,
        $cspNonce
    ) {
        $this->signature = $signature;
        $this->baseURL = $baseURL;
        $this->serviceUrl = URL::fromString((string) $config['serviceUrl']);
        $this->optimizerSizeDiffThreshold = (int) $config['optimizerSizeDiffThreshold'];
        $this->localRetriever = $localRetriever;
        $this->retriever = $retriever;
        $this->optimizerFactory = $optimizerFactory;
        $this->cssFilter = $cssFilter;
        $this->tokenRefMaker = $tokenRefMaker;
        $this->cspNonce = $cspNonce;

        foreach ($config['whitelist'] as $key => $value) {
            if (!is_array($value)) {
                $this->whitelist[$value] = ['ieCompatible' => true];
                $key = $value;
            } else {
                $this->whitelist[$key] = $value;
            }
        }
    }

    protected function beforeLoop() {
        $this->elements = iterator_to_array($this->elements);
        $this->optimizer = $this->optimizerFactory->makeForElements(new \ArrayIterator($this->elements));
    }

    protected function isTagOfInterest(Tag $tag) {
        return $tag->getTagName() == 'style'
               || (
                   $tag->getTagName() == 'link'
                  && $tag->getAttribute('rel') == 'stylesheet'
                  && $tag->hasAttribute('href')
               );
    }

    protected function handleTag(Tag $tag) {
        if ($tag->getTagName() == 'link') {
            return $this->inlineLink($tag, $this->context->getBaseUrl());
        }
        return $this->inlineStyle($tag);
    }

    protected function afterLoop() {
        $this->addIEFallbackScript();
        $this->addInlinedRetrieverScript();
    }

    private function inlineLink(Tag $link, URL $baseUrl) {
        $href = trim($link->getAttribute('href'));

        if (trim($href, '/') == '') {
            return [$link];
        }

        $location = URL::fromString($href)->withBase($baseUrl);

        if (!$this->findInWhitelist($location)
            && !$this->localRetriever->getCacheSalt($location)
        ) {
            return [$link];
        }

        $media = $link->getAttribute('media');

        if (preg_match(
            '~^\s*(this\.)?media\s*=\s*(?<q>[\'"])(?<m>((?!\k<q>).)+?)\k<q>\s*(;|$)~',
            $link->getAttribute('onload'),
            $match
        )) {
            $media = $match['m'];
        }

        $elements = $this->inlineURL($location, $media);
        return is_null($elements) ? [$link] : $elements;
    }

    private function inlineStyle(Tag $style) {
        $processed = $this->cssFilter
            ->apply(Resource::makeWithContent($this->baseURL, $style->textContent), [])
            ->getContent();
        $elements = $this->inlineCSS(
            $this->baseURL,
            $processed,
            $style->getAttribute('media'),
            false
        );
        if (($id = $style->getAttribute('id')) != '') {
            if (sizeof($elements) == 1) {
                $elements[0]->setAttribute('id', $id);
            } else {
                foreach ($elements as $element) {
                    $element->setAttribute('data-phast-original-id', $id);
                }
            }
        }
        return $elements;
    }

    private function findInWhitelist(URL $url) {
        $stringUrl = (string) $url;
        foreach ($this->whitelist as $pattern => $settings) {
            if (preg_match($pattern, $stringUrl)) {
                return $settings;
            }
        }
        return false;
    }

    /**
     * @param URL $url
     * @param string $media
     * @param boolean $ieCompatible
     * @param int $currentLevel
     * @param string[] $seen
     * @return Tag[]|null
     * @throws \Kibo\Phast\Exceptions\ItemNotFoundException
     */
    private function inlineURL(URL $url, $media, $ieCompatible = true, $currentLevel = 0, $seen = []) {
        $whitelistEntry = $this->findInWhitelist($url);

        if (!$whitelistEntry) {
            $whitelistEntry = !!$this->localRetriever->getCacheSalt($url);
        }

        if (!$whitelistEntry) {
            $this->logger()->info('Not inlining {url}. Not in whitelist', ['url' => ($url)]);
            return [$this->makeLink($url, $media)];
        }

        if (isset($whitelistEntry['ieCompatible']) && !$whitelistEntry['ieCompatible']) {
            $ieFallbackUrl = $ieCompatible ? $url : null;
            $ieCompatible = false;
        } else {
            $ieFallbackUrl = null;
        }

        if (in_array($url, $seen)) {
            return [];
        }

        if ($currentLevel > $this->maxInlineDepth) {
            return $this->addIEFallback($ieFallbackUrl, [$this->makeLink($url, $media)]);
        }

        $seen[] = $url;

        $this->logger()->info('Inlining {url}.', ['url' => (string) $url]);
        $content = $this->retriever->retrieve($url);
        if ($content === false) {
            return $this->addIEFallback(
                $ieFallbackUrl,
                [$this->makeServiceLink($url, $media)]
            );
        }

        $content = $this->cssFilter->apply(Resource::makeWithContent($url, $content), [])->getContent();

        $this->cacheMarkers[$url->toString()] = Base64url::shortHash(implode("\0", [
            $this->retriever->getCacheSalt($url),
            $content,
        ]));

        $optimized = $this->optimizer->optimizeCSS($content);
        if ($optimized === null) {
            $this->logger()->error('CSS optimizer failed for {url}', ['url' => (string) $url]);
            return null;
        }
        $isOptimized = false;
        if (strlen($content) - strlen($optimized) > $this->optimizerSizeDiffThreshold) {
            $content = $optimized;
            $isOptimized = true;
        }
        $elements = $this->inlineCSS(
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
            $matchedUrl = URL::fromString($match['url'])->withBase($url);
            $replacement = $this->inlineURL($matchedUrl, $media, $ieCompatible, $currentLevel + 1, $seen);
            if ($replacement !== null) {
                $content = str_replace($match[0], '', $content);
                $elements = array_merge($elements, $replacement);
            }
        }

        $elements[] = $this->makeStyle($url, $content, $media, $optimized);

        return $elements;
    }

    private function addIEFallback(URL $fallbackUrl = null, array $elements = null) {
        if ($fallbackUrl === null || !$elements) {
            return $elements;
        }

        foreach ($elements as $element) {
            $element->setAttribute('data-phast-nested-inlined', '');
        }

        $element->setAttribute('data-phast-ie-fallback-url', (string) $fallbackUrl);
        $element->removeAttribute('data-phast-nested-inlined');

        $this->logger()->info('Set {url} as IE fallback URL', ['url' => (string) $fallbackUrl]);

        return $elements;
    }

    private function addIEFallbackScript() {
        $this->logger()->info('Adding IE fallback script');
        $this->context->addPhastJavaScript(PhastJavaScript::fromFile(__DIR__ . '/ie-fallback.js'));
    }

    private function addInlinedRetrieverScript() {
        $this->logger()->info('Adding inlined retriever script');
        $this->context->addPhastJavaScript(PhastJavaScript::fromFile(__DIR__ . '/inlined-css-retriever.js'));
    }

    private function getImportedURLs($cssContent) {
        preg_match_all(
            self::CSS_IMPORTS_REGEXP,
            $cssContent,
            $matches,
            PREG_SET_ORDER
        );
        return $matches;
    }

    private function makeStyle(URL $url, $content, $media, $optimized, $stripImports = true) {
        $style = new Tag('style');
        if ($media !== '' && $media !== 'all') {
            $style->setAttribute('media', $media);
        }
        if ($optimized) {
            $style->setAttribute('data-phast-original-src', $url->toString());
            $style->setAttribute('data-phast-params', $this->makeServiceParams($url, $stripImports));
        }
        if ($this->cspNonce) {
            $style->setAttribute('nonce', $this->cspNonce);
        }
        $content = preg_replace('~(</)(style)~i', '$1 $2', $content);
        $style->setTextContent($content);
        return $style;
    }

    private function makeLink(URL $url, $media) {
        $link = new Tag('link', ['rel' => 'stylesheet', 'href' => (string) $url]);
        if ($media !== '') {
            $link->setAttribute('media', $media);
        }
        return $link;
    }

    private function makeServiceLink(URL $location, $media) {
        $url = $this->makeServiceURL($location);
        return $this->makeLink(URL::fromString($url), $media);
    }

    protected function makeServiceParams(URL $originalLocation, $stripImports = false) {
        if (isset($this->cacheMarkers[$originalLocation->toString()])) {
            $cacheMarker = $this->cacheMarkers[$originalLocation->toString()];
        } else {
            $cacheMarker = $this->retriever->getCacheSalt($originalLocation);
        }
        $src = $originalLocation;
        if ($this->localRetriever->getCacheSalt($src)) {
            $src = $originalLocation->withoutQuery();
        }
        $params = [
            'src' => (string) $src,
            'cacheMarker' => $cacheMarker,
        ];
        if ($stripImports) {
            $params['strip-imports'] = 1;
        }
        return ServiceParams::fromArray($params)
            ->sign($this->signature)
            ->replaceByTokenRef($this->tokenRefMaker)
            ->serialize();
    }

    protected function makeServiceURL(URL $originalLocation) {
        $params = [
            'service' => 'css',
            'src' => (string) $originalLocation,
            'cacheMarker' => $this->retriever->getCacheSalt($originalLocation),
        ];

        return (new ServiceRequest())
            ->withUrl($this->serviceUrl)
            ->withParams($params)
            ->sign($this->signature)
            ->serialize();
    }
}
