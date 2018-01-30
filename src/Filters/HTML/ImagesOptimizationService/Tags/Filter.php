<?php

namespace Kibo\Phast\Filters\HTML\ImagesOptimizationService\Tags;

use Kibo\Phast\Common\DOMDocument;
use Kibo\Phast\Filters\HTML\HTMLFilter;
use Kibo\Phast\Filters\HTML\ImagesOptimizationService\ImageURLRewriter;
use Kibo\Phast\ValueObjects\URL;

class Filter implements HTMLFilter {

    /**
     * @var ImageURLRewriter
     */
    private $rewriter;

    /**
     * @var URL
     */
    private $baseUrl;

    /**
     * Filter constructor.
     * @param ImageURLRewriter $rewriter
     */
    public function __construct(ImageURLRewriter $rewriter) {
        $this->rewriter = $rewriter;
    }

    public function transformHTMLDOM(DOMDocument $document) {
        $this->baseUrl = $document->getBaseURL();
        /** @var \DOMElement $img */
        foreach ($document->query('//img') as $img) {
            $this->rewriteSrc($img);
            $this->rewriteSrcset($img);
        }
    }

    private function rewriteSrc(\DOMElement $img) {
        $url = $this->rewriter->makeURLAbsoluteToBase($img->getAttribute('src'), $this->baseUrl);
        if (!$this->rewriter->shouldRewriteUrl($url)) {
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
            $this->rewriter->makeSignedUrl($params)
        );
    }

    private function rewriteSrcset(\DOMElement $img) {
        $srcset = $img->getAttribute('srcset');
        if (!$srcset) {
            return;
        }
        $rewritten = preg_replace_callback('/([^,\s]+)(\s+(?:[^,]+))?/', function ($match) {
            $url = $this->rewriter->makeURLAbsoluteToBase($match[1], $this->baseUrl);
            if ($this->rewriter->shouldRewriteUrl($url)) {
                $params = ['src' => $url];
                $url = $this->rewriter->makeSignedUrl($params);
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
}
