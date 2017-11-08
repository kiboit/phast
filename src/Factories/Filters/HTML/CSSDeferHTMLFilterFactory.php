<?php

namespace Kibo\Phast\Factories\Filters\HTML;

use Kibo\Phast\Filters\HTML\CSSDeferHTMLFilter;

class CSSDeferHTMLFilterFactory implements HTMLFilterFactory {

    public function make(array $config) {
        return new CSSDeferHTMLFilter();
    }

}
