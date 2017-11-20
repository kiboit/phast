<?php

namespace Kibo\Phast\Filters\HTML;

use Kibo\Phast\Filters\HTML\Helpers\SignedUrlMakerTrait;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\ValueObjects\URL;

class ImagesOptimizationServiceHTMLFilter implements HTMLFilter {
    use SignedUrlMakerTrait;

    /**
     * @var ServiceSignature
     */
    protected $signature;

    /**
     * @var URL
     */
    protected $baseUrl;

    /**
     * @var URL
     */
    protected $serviceUrl;

    /**
     * @var string[]
     */
    protected $whitelist;

    /**
     * ImagesOptimizationServiceHTMLFilter constructor.
     *
     * @param ServiceSignature $signature
     * @param URL $baseUrl
     * @param URL $serviceUrl
     * @param string[] $whitelist
     */
    public function __construct(ServiceSignature $signature, URL $baseUrl, URL $serviceUrl, array $whitelist) {
        $this->signature = $signature;
        $this->baseUrl = $baseUrl;
        $this->serviceUrl = $serviceUrl;
        $this->whitelist = $whitelist;
    }

    public function transformHTMLDOM(\DOMDocument $document) {
        $images = $document->getElementsByTagName('img');
        /** @var \DOMElement $img */
        foreach ($images as $img) {
            if ($this->shouldRewriteSrc($img)) {
                $this->rewriteSrc($img);
            }
            if ($img->hasAttribute('srcset')) {
                $this->rewriteSrcset($img);
            }
        }
    }

    private function shouldRewriteSrc(\DOMElement $img) {
        return $img->hasAttribute('src') && $this->shouldRewriteUrl($img->getAttribute('src'));
    }

    private function rewriteSrc(\DOMElement $img) {
        $params = ['src' => (string) URL::fromString($img->getAttribute('src'))->withBase($this->baseUrl)];
        foreach (['width', 'height'] as $attr) {
            $value = $img->getAttribute($attr);
            if (preg_match('/^[1-9][0-9]*$/', $value)) {
                $params[$attr] = $value;
            }
        }
        $img->setAttribute(
            'src',
            $this->makeSignedUrl($this->serviceUrl, $params, $this->signature)
        );
    }

    private function rewriteSrcset(\DOMElement $img) {
        $rewritten = preg_replace_callback('/([^,\s]+)(\s+(?:[^,]+))?/', function ($match) {
            if ($this->shouldRewriteUrl($match[1])) {
                $params = ['src' => (string) URL::fromString($match[1])->withBase($this->baseUrl)];
                $url =  $this->makeSignedUrl($this->serviceUrl, $params, $this->signature);
            } else {
                $url = $match[1];
            }
            if (isset ($match[2])) {
                return $url . $match[2];
            }
            return $url;
        }, $img->getAttribute('srcset'));
        $img->setAttribute('srcset', $rewritten);
    }

    protected function shouldRewriteUrl($url) {
        if (substr($url, 0, 5) === 'data:') {
            return false;
        }
        $absolute = URL::fromString($url)->withBase($this->baseUrl)->toString();
        foreach ($this->whitelist as $pattern) {
            if (preg_match($pattern, $absolute)) {
                return true;
            }
        }
        return false;
    }

}
