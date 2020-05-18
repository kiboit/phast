<?php

namespace Kibo\Phast\Filters\HTML\ImagesOptimizationService\Tags;

use Kibo\Phast\Filters\HTML\HTMLPageContext;
use Kibo\Phast\Filters\HTML\HTMLStreamFilter;
use Kibo\Phast\Filters\HTML\ImagesOptimizationService\ImageURLRewriter;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\ClosingTag;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;

class Filter implements HTMLStreamFilter {
    /**
     * @var ImageURLRewriter
     */
    private $rewriter;

    private $inPictureTag = false;

    /**
     * Filter constructor.
     * @param ImageURLRewriter $rewriter
     */
    public function __construct(ImageURLRewriter $rewriter) {
        $this->rewriter = $rewriter;
    }

    public function transformElements(\Traversable $elements, HTMLPageContext $context) {
        foreach ($elements as $element) {
            if ($element instanceof Tag) {
                $this->handleTag($element, $context);
            } elseif ($element instanceof ClosingTag) {
                $this->handleClosingTag($element);
            }
            yield $element;
        }
    }

    private function handleTag(Tag $tag, HTMLPageContext $context) {
        if ($tag->getTagName() == 'img' || ($this->inPictureTag && $tag->getTagName() == 'source')) {
            foreach (['', 'data-', 'data-lazy-', 'data-wood-'] as $prefix) {
                $this->rewriteSrc($tag, $context, $prefix . 'src');
                $this->rewriteSrcset($tag, $context, $prefix . 'srcset');
            }
        } elseif ($tag->getTagName() == 'picture') {
            $this->inPictureTag = true;
        } elseif ($tag->getTagName() == 'video' || $tag->getTagName() == 'audio') {
            $this->inPictureTag = false;
        }
    }

    private function handleClosingTag(ClosingTag $closingTag) {
        if ($closingTag->getTagName() == 'picture') {
            $this->inPictureTag = false;
        }
    }

    private function rewriteSrc(Tag $img, HTMLPageContext $context, $attribute) {
        $url = $img->getAttribute($attribute);
        if (!$url) {
            return;
        }
        $params = [];
        foreach (['width', 'height'] as $attr) {
            $value = $img->getAttribute($attr);
            if (preg_match('/^[1-9][0-9]*$/', $value)) {
                $params[$attr] = $value;
            }
        }
        $newURL = $this->rewriter->rewriteUrl($url, $context->getBaseUrl(), $params);
        $img->setAttribute($attribute, $newURL);
    }

    private function rewriteSrcset(Tag $img, HTMLPageContext $context, $attribute) {
        $srcset = $img->getAttribute($attribute);
        if (!$srcset) {
            return;
        }
        $rewritten = preg_replace_callback('/([^,\s]+)(\s+(?:[^,]+))?/', function ($match) use ($context) {
            $url = $this->rewriter->rewriteUrl($match[1], $context->getBaseUrl());
            if (isset($match[2])) {
                return $url . $match[2];
            }
            return $url;
        }, $srcset);
        $img->setAttribute($attribute, $rewritten);
    }
}
