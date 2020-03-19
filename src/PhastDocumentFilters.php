<?php

namespace Kibo\Phast;

use Kibo\Phast\Common\OutputBufferHandler;
use Kibo\Phast\Environment\Configuration;
use Kibo\Phast\Filters\HTML\Composite\Factory;
use Kibo\Phast\HTTP\Request;
use Kibo\Phast\Logging\Log;
use Kibo\Phast\Services\ServiceRequest;

class PhastDocumentFilters {
    const DOCUMENT_PATTERN = "~
        \s* (<\?xml[^>]*>)?
        (\s* <!--(.*?)-->)*
        \s* (<!doctype\s+html[^>]*>)?
        (\s* <!--(.*?)-->)*
        \s* <html (?! [^>]* \s ( amp | ⚡ ) [\s=>] )
        .*
        ( </body> | </html> )
    ~xsiA";

    /**
     * @return ?OutputBufferHandler
     */
    public static function deploy(array $userConfig = []) {
        $runtimeConfig = self::configure($userConfig);
        if (!$runtimeConfig) {
            return null;
        }
        $handler = new OutputBufferHandler(
            $runtimeConfig['documents']['maxBufferSizeToApply'],
            function ($html, $applyCheckBuffer) use ($runtimeConfig) {
                return self::applyWithRuntimeConfig($html, $runtimeConfig, $applyCheckBuffer);
            }
        );
        $handler->install();
        Log::info('Phast deployed!');
        return $handler;
    }

    public static function apply($html, array $userConfig) {
        $runtimeConfig = self::configure($userConfig);
        if (!$runtimeConfig) {
            return $html;
        }
        return self::applyWithRuntimeConfig($html, $runtimeConfig);
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

    private static function applyWithRuntimeConfig($buffer, $runtimeConfig, $applyCheckBuffer = null) {
        if (is_null($applyCheckBuffer)) {
            $applyCheckBuffer = $buffer;
        }
        if (!self::shouldApply($applyCheckBuffer, $runtimeConfig)) {
            Log::info("Buffer ({bufferSize} bytes) doesn't look like html! Not applying filters", ['bufferSize' => strlen($applyCheckBuffer)]);
            return $buffer;
        }
        return (new Factory())
            ->make($runtimeConfig)
            ->apply($buffer);
    }

    private static function shouldApply($buffer, $runtimeConfig) {
        if ($runtimeConfig['optimizeHTMLDocumentsOnly']) {
            return preg_match(self::DOCUMENT_PATTERN, $buffer);
        }
        return strpos($buffer, '<') !== false;
    }
}
