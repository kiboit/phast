<?php
namespace Kibo\Phast\Filters\HTML\LazyImageLoading;

use Kibo\Phast\Filters\HTML\BaseHTMLStreamFilter;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;

class Filter extends BaseHTMLStreamFilter {
    protected function handleTag(Tag $tag) {
        if (!$tag->hasAttribute('loading')) {
            $tag->setAttribute('loading', 'lazy');
        }
        yield $tag;
    }

    protected function isTagOfInterest(Tag $tag) {
        return $tag->getTagName() == 'img';
    }
}
