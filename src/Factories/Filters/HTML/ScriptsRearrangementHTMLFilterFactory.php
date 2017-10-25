<?php

namespace Kibo\Phast\Factories\Filters\HTML;

use Kibo\Phast\Filters\HTML\ScriptsRearrangementHTMLFilter;

class ScriptsRearrangementHTMLFilterFactory implements HTMLFilterFactory {

    public function make(array $config) {
        return new ScriptsRearrangementHTMLFilter();
    }

}
