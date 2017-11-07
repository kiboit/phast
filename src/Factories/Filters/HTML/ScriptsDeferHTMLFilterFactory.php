<?php

namespace Kibo\Phast\Factories\Filters\HTML;

use Kibo\Phast\Filters\HTML\ScriptsDeferHTMLFilter;

class ScriptsDeferHTMLFilterFactory implements HTMLFilterFactory {

    public function make(array $config) {
        return new ScriptsDeferHTMLFilter();
    }

}
