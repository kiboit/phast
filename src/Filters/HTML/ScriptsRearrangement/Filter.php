<?php

namespace Kibo\Phast\Filters\HTML\ScriptsRearrangement;

use Kibo\Phast\Common\DOMDocument;
use Kibo\Phast\Filters\HTML\Helpers\JSDetectorTrait;
use Kibo\Phast\Filters\HTML\RearrangementHTMLFilter;

class Filter extends RearrangementHTMLFilter {
    use JSDetectorTrait;

    protected function getElementsToRearrange(DOMDocument $document) {
        $scripts = $document->query('//script');
        foreach ($scripts as $script) {
            if ($this->isJSElement($script)) {
                yield $script;
            }
        }
    }
}
