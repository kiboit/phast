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
        $images = (new \DOMXPath($document))->query('//img');
        /** @var \DOMElement $img */
        foreach ($images as $img) {
            $this->rewriteSrc($img);
            $this->rewriteSrcset($img);
        }
    }

    private function rewriteSrc(\DOMElement $img) {
        if (!($url = $this->shouldRewriteUrl($img->getAttribute('src')))) {
            return;
        }
        $params = ['src' => $url];
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
        $srcset = $img->getAttribute('srcset');
        if (!$srcset) {
            return;
        }
        $rewritten = preg_replace_callback('/([^,\s]+)(\s+(?:[^,]+))?/', function ($match) {
            if ($url = $this->shouldRewriteUrl($match[1])) {
                $params = ['src' => $url];
                $url = $this->makeSignedUrl($this->serviceUrl, $params, $this->signature);
            } else {
                $url = $match[1];
            }
            if (isset ($match[2])) {
                return $url . $match[2];
            }
            return $url;
        }, $srcset);
        $img->setAttribute('srcset', $rewritten);
    }

    protected function shouldRewriteUrl($url) {
        if (!$url || substr($url, 0, 5) === 'data:') {
            return;
        }
        $absolute = URL::fromString($url)->withBase($this->baseUrl)->toString();
        foreach ($this->whitelist as $pattern) {
            if (preg_match($pattern, $absolute)) {
                return $absolute;
            }
        }
    }

}
