<?php

namespace Kibo\Phast\Filters\HTML\DelayedIFrameLoading;

use Kibo\Phast\Filters\HTML\BaseHTMLStreamFilter;
use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;
use Kibo\Phast\ValueObjects\PhastJavaScript;

class Filter extends BaseHTMLStreamFilter {
    use LoggingTrait;

    protected $addScript = false;

    protected function isTagOfInterest(Tag $tag) {
        return $tag->getTagName() == 'iframe' && $tag->hasAttribute('src');
    }

    protected function handleTag(Tag $iframe) {
        $this->logger()->info('Delaying iframe {src}', ['src' => $iframe->getAttribute('src')]);
        $iframe->setAttribute('data-phast-src', $iframe->getAttribute('src'));
        $iframe->setAttribute('src', 'about:blank');
        $this->addScript = true;
        yield $iframe;
    }

    protected function onBodyEnd() {
        if ($this->addScript) {
            $this->context->addPhastJavaScript(new PhastJavaScript(__DIR__ . '/iframe-loader.js'));
        }
    }

}
