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
     * ImagesOptimizationServiceHTMLFilter constructor.
     *
     * @param ServiceSignature $signature
     * @param URL $baseUrl
     * @param URL $serviceUrl
     */
    public function __construct(ServiceSignature $signature, URL $baseUrl, URL $serviceUrl) {
        $this->signature = $signature;
        $this->baseUrl = $baseUrl;
        $this->serviceUrl = $serviceUrl;
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
        return $img->hasAttribute('src') && substr($img->getAttribute('src'), 0, 5) !== 'data:';
    }

    private function rewriteSrc(\DOMElement $img) {
        $params = ['src' => (string) URL::fromString($img->getAttribute('src'))->withBase($this->baseUrl)];
        foreach (['width', 'height'] as $attr) {
            if ($img->hasAttribute($attr)) {
                $params[$attr] = $img->getAttribute($attr);
            }
        }
        $img->setAttribute(
            'src',
            $this->makeSignedUrl($this->serviceUrl, $params, $this->signature)
        );
    }

    private function rewriteSrcset(\DOMElement $img) {
        $rewritten = preg_replace_callback('/([^,\s]+)(\s+(?:[^,]+))?/', function ($match) {
            if (substr($match[1], 0, 5) === 'data:') {
                $url = $match[1];
            } else {
                $params = ['src' => (string) URL::fromString($match[1])->withBase($this->baseUrl)];
                $url =  $this->makeSignedUrl($this->serviceUrl, $params, $this->signature);
            }
            if (isset ($match[2])) {
                return $url . $match[2];
            }
            return $url;
        }, $img->getAttribute('srcset'));
        $img->setAttribute('srcset', $rewritten);
    }

}
