<?php

namespace Kibo\Phast\Filters\HTML;

use Kibo\Phast\Filters\HTML\Helpers\JSDetectorTrait;

class ScriptsRearrangementHTMLFilter extends RearrangementHTMLFilter {
    use JSDetectorTrait;

    protected function getElementsToRearrange(\Kibo\Phast\Common\DOMDocument $document) {
        $scripts = $document->query('//script');
        foreach ($scripts as $script) {
            if ($this->isJSElement($script)) {
                yield $script;
            }
        }
    }
}
