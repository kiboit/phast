<?php

namespace Kibo\Phast\Filters\HTML\ScriptsDeferring;

use Kibo\Phast\Filters\HTML\BaseHTMLPageContextFilter;
use Kibo\Phast\Filters\HTML\Helpers\JSDetectorTrait;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;
use Kibo\Phast\ValueObjects\PhastJavaScript;

class Filter extends BaseHTMLPageContextFilter {
    use JSDetectorTrait;

    protected function isTagOfInterest(Tag $tag) {
        return $tag->getTagName() == 'script';
    }

    protected function handleTag(Tag $script) {
        if ($script->hasAttribute('data-phast-no-defer')) {
            $script->removeAttribute('data-phast-no-defer');
        } elseif ($this->isJSElement($script)) {
            $this->rewrite($script);
        }
        yield $script;
    }

    protected function afterLoop() {
        $this->context->addPhastJavaScript(new PhastJavaScript(__DIR__ . '/rewrite.js'));
    }


    private function rewrite(Tag $script) {
        if ($script->hasAttribute('type')) {
            $script->setAttribute('data-phast-original-type', $script->getAttribute('type'));
        }
        $script->setAttribute('type', 'phast-script');
    }

}
