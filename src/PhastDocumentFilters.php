<?php

namespace Kibo\Phast;

use Kibo\Phast\Logging\Log;
use Kibo\Phast\Factories\Filters\HTML\CompositeHTMLFilterFactory;
use Kibo\Phast\HTTP\Request;
use Kibo\Phast\Services\ServiceRequest;

class PhastDocumentFilters {

    public static function deploy(array $config) {
        $request = ServiceRequest::fromHTTPRequest(Request::fromGlobals());
        Log::init($config['logging'], $request, 'dom-filters');
        $filter = (new CompositeHTMLFilterFactory())->make($config);
        Log::info('Phast deployed!');
        ob_start([$filter, 'apply']);
    }

}
