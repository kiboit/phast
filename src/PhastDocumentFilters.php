<?php

namespace Kibo\Phast;

use Kibo\Phast\Environment\Configuration;
use Kibo\Phast\Logging\Log;
use Kibo\Phast\Factories\Filters\HTML\CompositeHTMLFilterFactory;
use Kibo\Phast\HTTP\Request;
use Kibo\Phast\Services\ServiceRequest;

class PhastDocumentFilters {

    public static function deploy(array $config) {
        $request = ServiceRequest::fromHTTPRequest(Request::fromGlobals());
        $runtimeConfig = (new Configuration($config))->withServiceRequest($request)->toArray();
        Log::init($runtimeConfig['logging'], $request, 'dom-filters');
        if (!$runtimeConfig['switches']['phast']) {
            Log::info('Phast is off. Skipping document filter deployment!');
            return;
        }
        $filter = (new CompositeHTMLFilterFactory())->make($runtimeConfig);
        Log::info('Phast deployed!');
        ob_start([$filter, 'apply']);
    }

}
