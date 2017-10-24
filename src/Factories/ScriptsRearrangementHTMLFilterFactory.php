<?php

namespace Kibo\Phast\Factories;

use Kibo\Phast\Filters\ScriptsRearrangementHTMLFilter;

class ScriptsRearrangementHTMLFilterFactory implements HTMLFilterFactory {

    public function make(array $config) {
        return new ScriptsRearrangementHTMLFilter();
    }

}
