<?php

namespace Kibo\Phast\Factories;

use Kibo\Phast\Filters\CSSInliningHTMLFilter;

class CSSInliningHTMLFilterFactory implements HTMLFilterFactory {

    public function make(array $config) {
        return new CSSInliningHTMLFilter($config['baseURL'], function ($file) {
            return @file_get_contents($file);
        });
    }

}
