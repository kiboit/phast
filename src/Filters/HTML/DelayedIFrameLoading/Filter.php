<?php
namespace Kibo\Phast\Filters\HTML\DelayedIFrameLoading;

use Kibo\Phast\Filters\HTML\BaseHTMLStreamFilter;
use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;

class Filter extends BaseHTMLStreamFilter {
    use LoggingTrait;

    protected function isTagOfInterest(Tag $tag) {
        return $tag->getTagName() == 'iframe'
               && $tag->hasAttribute('src');
    }

    protected function handleTag(Tag $tag) {
        if (!$tag->hasAttribute('loading')) {
            $tag->setAttribute('loading', 'lazy');
        }
        yield $tag;
    }
}
