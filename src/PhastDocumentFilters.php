<?php

namespace Kibo\Phast;

use Kibo\Phast\Common\OutputBufferHandler;
use Kibo\Phast\Environment\Configuration;
use Kibo\Phast\Filters\HTML\Composite\Factory;
use Kibo\Phast\HTTP\Request;
use Kibo\Phast\Logging\Log;
use Kibo\Phast\Services\ServiceRequest;

class PhastDocumentFilters {

    public static function deploy(array $userConfig) {
        $runtimeConfig = self::configure($userConfig);

        if (!$runtimeConfig) {
            return;
        }

        $filter = (new Factory())->make($runtimeConfig);

        $handler = new OutputBufferHandler(
            $runtimeConfig['documents']['maxBufferSizeToApply'],
            [$filter, 'apply']
        );
        $handler->install();

        Log::info('Phast deployed!');
    }

    public static function apply($html, array $userConfig) {
        $runtimeConfig = self::configure($userConfig);

        if (!$runtimeConfig) {
            return $html;
        }

        if ($runtimeConfig['optimizeHTMLDocumentsOnly'] && !preg_match(OutputBufferHandler::DOCUMENT_PATTERN, $html)) {
            return $html;
        }

        $filter = (new Factory())->make($runtimeConfig);

        return $filter->apply($html);
    }

    private static function configure(array $userConfig) {
        $request = ServiceRequest::fromHTTPRequest(Request::fromGlobals());

        $runtimeConfig = Configuration::fromDefaults()
            ->withUserConfiguration(new Configuration($userConfig))
            ->withServiceRequest($request)
            ->getRuntimeConfig()
            ->toArray();

        Log::init($runtimeConfig['logging'], $request, 'dom-filters');

        ServiceRequest::setDefaultSerializationMode($runtimeConfig['serviceRequestFormat']);

        if ($request->hasRequestSwitchesSet()) {
            Log::info('Request has switches set! Sending "noindex" header!');
            header('X-Robots-Tag: noindex');
        }

        if (!$runtimeConfig['switches']['phast']) {
            Log::info('Phast is off. Skipping document filter deployment!');
            return;
        }

        return $runtimeConfig;
    }

}
