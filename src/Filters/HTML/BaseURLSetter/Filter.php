<?php


namespace Kibo\Phast\Filters\HTML\BaseURLSetter;

use Kibo\Phast\Filters\HTML\BaseHTMLStreamFilter;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;
use Kibo\Phast\ValueObjects\URL;

class Filter extends BaseHTMLStreamFilter {
    protected function isTagOfInterest(Tag $tag) {
        return $tag->getTagName() == 'base' && $tag->hasAttribute('href');
    }

    protected function handleTag(Tag $tag) {
        $base = URL::fromString($tag->getAttribute('href'));
        $current = $this->context->getBaseUrl();
        $this->context->setBaseUrl($base->withBase($current));
        yield $tag;
    }
}
