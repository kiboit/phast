<?php

namespace Kibo\Phast\Filters\HTML\DelayedIFrameLoading;

use Kibo\Phast\Filters\HTML\BaseHTMLStreamFilter;
use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;
use Kibo\Phast\ValueObjects\PhastJavaScript;

class Filter extends BaseHTMLStreamFilter {
    use LoggingTrait;

    protected $addScript = false;

    private $ignoredUrlPattern = '~
        ^about: |
        ^data:
    ~ix';

    protected function isTagOfInterest(Tag $tag) {
        return $tag->getTagName() == 'iframe'
               && $tag->hasAttribute('src');
    }

    protected function handleTag(Tag $iframe) {
        $src = trim($iframe->getAttribute('src'));
        if (preg_match($this->ignoredUrlPattern, $src)) {
            yield $iframe;
            return;
        }
        $this->logger()->info('Delaying iframe {src}', ['src' => $src]);
        $iframe->setAttribute('data-phast-src', $src);
        $iframe->setAttribute('src', 'about:blank');
        $this->addScript = true;
        yield $iframe;
    }

    protected function afterLoop() {
        if ($this->addScript) {
            $this->context->addPhastJavaScript(PhastJavaScript::fromFile(__DIR__ . '/iframe-loader.js'));
        }
    }
}
