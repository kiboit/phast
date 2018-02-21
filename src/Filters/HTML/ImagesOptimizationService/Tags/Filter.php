<?php

namespace Kibo\Phast\Filters\HTML\ImagesOptimizationService\Tags;

use Kibo\Phast\Filters\HTML\BaseHTMLStreamFilter;
use Kibo\Phast\Filters\HTML\ImagesOptimizationService\ImageURLRewriter;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;
use Kibo\Phast\ValueObjects\URL;

class Filter extends BaseHTMLStreamFilter {

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

    public function beforeLoop() {
        $this->baseUrl = $this->context->getBaseUrl();
    }

    public function isTagOfInterest(Tag $tag) {
        return $tag->getTagName() == 'img';
    }

    protected function handleTag(Tag $tag) {
        $this->rewriteSrc($tag);
        $this->rewriteSrcset($tag);
        yield $tag;
    }

    private function rewriteSrc(Tag $img) {
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

    private function rewriteSrcset(Tag $img) {
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
