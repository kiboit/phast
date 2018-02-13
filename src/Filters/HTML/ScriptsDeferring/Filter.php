<?php

namespace Kibo\Phast\Filters\HTML\ScriptsDeferring;

use Kibo\Phast\Common\DOMDocument;
use Kibo\Phast\Filters\HTML\Helpers\JSDetectorTrait;
use Kibo\Phast\Filters\HTML\HTMLFilter;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;
use Kibo\Phast\ValueObjects\PhastJavaScript;

class Filter implements HTMLFilter {
    use JSDetectorTrait;

    public function transformHTMLDOM(DOMDocument $document) {
        foreach ($document->query('//script') as $script) {
            if ($script->hasAttribute('data-phast-no-defer')) {
                $script->removeAttribute('data-phast-no-defer');
            } elseif ($this->isJSElement($script)) {
                $this->rewrite($script);
            }
        }
        $document->addPhastJavaScript(new PhastJavaScript(__DIR__ . '/rewrite.js'));
    }

    private function rewrite(Tag $script) {
        if ($script->hasAttribute('type')) {
            $script->setAttribute('data-phast-original-type', $script->getAttribute('type'));
        }
        $script->setAttribute('type', 'phast-script');
    }

}
