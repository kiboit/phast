<?php

namespace Kibo\Phast\Filters;

use Kibo\Phast\Security\ImagesOptimizationSignature;
use Kibo\Phast\ValueObjects\URL;

class ImagesOptimizationServiceHTMLFilter implements HTMLFilter {

    /**
     * @var ImagesOptimizationSignature
     */
    private $signature;

    /**
     * @var URL
     */
    private $baseUrl;

    /**
     * @var URL
     */
    private $serviceUrl;

    /**
     * ImagesOptimizationServiceHTMLFilter constructor.
     *
     * @param ImagesOptimizationSignature $signature
     * @param URL $baseUrl
     * @param URL $serviceUrl
     */
    public function __construct(ImagesOptimizationSignature $signature, URL $baseUrl, URL $serviceUrl) {
        $this->signature = $signature;
        $this->baseUrl = $baseUrl;
        $this->serviceUrl = $serviceUrl;
    }

    public function transformHTMLDOM(\DOMDocument $document) {
        $images = $document->getElementsByTagName('img');
        foreach ($images as $img) {
            if ($this->shouldRewriteSrc($img)) {
                $this->rewriteSrc($img);
            }
        }
    }

    private function shouldRewriteSrc(\DOMElement $img) {
        if (!$img->hasAttribute('src') || $img->hasAttribute('srcset')) {
            return false;
        }
        return substr($img->getAttribute('src'), 0, 5) !== 'data:';
    }

    private function rewriteSrc(\DOMElement $img) {
        $parts = [['src', URL::fromString($img->getAttribute('src'))->withBase($this->baseUrl)]];
        foreach (['width', 'height'] as $attr) {
            if ($img->hasAttribute($attr)) {
                $parts[] = [$attr, $img->getAttribute($attr)];
            }
        }
        $query = join('&', array_map(function ($item) {
            return $item[0] . '=' . urlencode($item[1]);
        }, $parts));
        $query .= '&token=' . $this->signature->sign($query);
        $img->setAttribute('src', $this->serviceUrl . '?' . $query);
    }

}
