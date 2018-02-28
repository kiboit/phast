<?php

namespace Kibo\Phast\Filters\HTML\ImagesOptimizationService\Tags;

use Kibo\Phast\Filters\HTML\BaseHTMLStreamFilter;
use Kibo\Phast\Filters\HTML\ImagesOptimizationService\ImageURLRewriter;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;

class Filter extends BaseHTMLStreamFilter {

    /**
     * @var ImageURLRewriter
     */
    private $rewriter;

    /**
     * Filter constructor.
     * @param ImageURLRewriter $rewriter
     */
    public function __construct(ImageURLRewriter $rewriter) {
        $this->rewriter = $rewriter;
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
        $url = $img->getAttribute('src');
        $params = [];
        foreach (['width', 'height'] as $attr) {
            $value = $img->getAttribute($attr);
            if (preg_match('/^[1-9][0-9]*$/', $value)) {
                $params[$attr] = $value;
            }
        }
        $newURL = $this->rewriter->rewriteUrl($url, $this->context->getBaseUrl(), $params);
        if ($newURL != $url) {
            $img->setAttribute('src', $newURL);
        }
    }

    private function rewriteSrcset(Tag $img) {
        $srcset = $img->getAttribute('srcset');
        if (!$srcset) {
            return;
        }
        $rewritten = preg_replace_callback('/([^,\s]+)(\s+(?:[^,]+))?/', function ($match) {
            $url = $this->rewriter->rewriteUrl($match[1], $this->context->getBaseUrl());
            if (isset ($match[2])) {
                return $url . $match[2];
            }
            return $url;
        }, $srcset);
        if ($rewritten != $srcset) {
            $img->setAttribute('srcset', $rewritten);
        }
    }
}
