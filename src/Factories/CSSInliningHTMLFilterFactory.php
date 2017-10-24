<?php

namespace Kibo\Phast\Factories;

use Kibo\Phast\Filters\CSSInliningHTMLFilter;
use Kibo\Phast\ValueObjects\URL;

class CSSInliningHTMLFilterFactory implements HTMLFilterFactory {

    public function make(array $config) {
        return new CSSInliningHTMLFilter(URL::fromString($config['baseURL']), function ($file) {
            return @file_get_contents($file);
        });
    }

}
