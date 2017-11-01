<?php

namespace Kibo\Phast\Factories\Filters\HTML;

use Kibo\Phast\Filters\HTML\CSSRearrangementHTMLFilter;
use Kibo\Phast\ValueObjects\URL;

class CSSRearrangementHTMLFilterFactory implements HTMLFilterFactory {

    public function make(array $config) {
        return new CSSRearrangementHTMLFilter(
            URL::fromString($config['documents']['baseUrl'])
        );
    }

}
