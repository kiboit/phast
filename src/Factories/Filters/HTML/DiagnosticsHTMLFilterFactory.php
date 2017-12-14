<?php

namespace Kibo\Phast\Factories\Filters\HTML;

use Kibo\Phast\Filters\HTML\DiagnosticsHTMLFilter;

class DiagnosticsHTMLFilterFactory implements HTMLFilterFactory {

    public function make(array $config) {
        $url =  isset ($config['documents']['filters'][DiagnosticsHTMLFilter::class]['serviceUrl'])
                ? $config['documents']['filters'][DiagnosticsHTMLFilter::class]['serviceUrl']
                : $config['servicesUrl'] . '?service=diagnostics';
        return new DiagnosticsHTMLFilter($url);
    }


}
