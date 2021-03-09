<?php

namespace Kibo\Phast;

use Kibo\Phast\Common\OutputBufferHandler;
use Kibo\Phast\Environment\Configuration;
use Kibo\Phast\Filters\HTML\AMPCompatibleFilter;
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
        \s* <html (?<amp> [^>]* \s ( amp | ⚡ ) [\s=>] )?
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
        if (preg_match('~^\s*{~', $buffer) && is_object($jsonData = json_decode($buffer))) {
            return self::applyToJson($jsonData, $buffer, $runtimeConfig);
        }
        if (is_null($applyCheckBuffer)) {
            $applyCheckBuffer = $buffer;
        }
        if (!self::shouldApply($applyCheckBuffer, $runtimeConfig)) {
            Log::info("Buffer ({bufferSize} bytes) doesn't look like html! Not applying filters", ['bufferSize' => strlen($applyCheckBuffer)]);
            return $buffer;
        }
        $compositeFilter = (new Factory())->make($runtimeConfig);
        if (self::isAMP($applyCheckBuffer)) {
            $compositeFilter->selectFilters(function ($filter) {
                return $filter instanceof AMPCompatibleFilter;
            });
        }
        return $compositeFilter->apply($buffer);
    }

    private static function applyToJson($jsonData, $buffer, $runtimeConfig) {
        if (!$runtimeConfig['optimizeJSONResponses']) {
            return $buffer;
        }
        if (empty($jsonData->html) || !is_string($jsonData->html)) {
            return $buffer;
        }
        $newHtml = self::applyWithRuntimeConfig($jsonData->html, $runtimeConfig);
        if ($newHtml == $jsonData->html) {
            return $buffer;
        }
        $jsonData->html = $newHtml;
        $json = json_encode($jsonData);
        if (!$json) {
            return $buffer;
        }
        return $json;
    }

    private static function shouldApply($buffer, $runtimeConfig) {
        if ($runtimeConfig['optimizeHTMLDocumentsOnly']) {
            return preg_match(self::DOCUMENT_PATTERN, $buffer);
        }
        return strpos($buffer, '<') !== false
               && !preg_match('~^\s*+{\s*+"~', $buffer);
    }

    private static function isAMP($buffer) {
        return preg_match(self::DOCUMENT_PATTERN, $buffer, $match) && !empty($match['amp']);
    }
}
