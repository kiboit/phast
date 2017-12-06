<?php

namespace Kibo\Phast;

use Kibo\Phast\Diagnostics\Log;
use Kibo\Phast\Factories\Filters\HTML\CompositeHTMLFilterFactory;
use Kibo\Phast\HTTP\Request;

class PhastDocumentFilters {

    public static function deploy(array $config) {
        Log::init($config['diagnostics']['logWriter'], Request::fromGlobals(), 'dom-filters');
        $filter = (new CompositeHTMLFilterFactory())->make($config);
        Log::info('Phast deployed!');
        ob_start([$filter, 'apply']);
    }

}
