<?php

namespace Kibo\Phast;

use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Environment\Configuration;
use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\Exceptions\UnauthorizedException;
use Kibo\Phast\HTTP\Request;
use Kibo\Phast\HTTP\Response;
use Kibo\Phast\Logging\Log;
use Kibo\Phast\Services\Factory;
use Kibo\Phast\Services\ServiceRequest;

class PhastServices {

    public static function serve(callable $getConfig) {
        $serviceRequest = ServiceRequest::fromHTTPRequest(
            Request::fromGlobals()
        );
        $serviceParams = $serviceRequest->getParams();

        if (defined('PHAST_SERVICE')) {
            $service = PHAST_SERVICE;
        } else if (!isset ($serviceParams['service'])) {
            http_response_code(404);
            exit;
        } else {
            $service = $serviceParams['service'];
        }

        if (isset ($serviceParams['src']) && !headers_sent())  {
            http_response_code(301);
            header('Location: ' . $serviceParams['src']);
            header('Cache-Control: max-age=86400');
        }

        try {
            $userConfig = new Configuration($getConfig());
            $runtimeConfig = Configuration::fromDefaults()
                ->withUserConfiguration($userConfig)
                ->withServiceRequest($serviceRequest)
                ->getRuntimeConfig()
                ->toArray();
            Log::init($runtimeConfig['logging'], $serviceRequest, $service);
            ServiceRequest::setDefaultSerializationMode($runtimeConfig['serviceRequestFormat']);

            Log::info('Starting service');
            $response = (new Factory())
                ->make($service, $runtimeConfig)
                ->serve($serviceRequest);
            Log::info('Service completed!');
        } catch (UnauthorizedException $e) {
            Log::error('Unauthorized exception: {message}!', ['message' => $e->getMessage()]);
            exit();
        } catch (ItemNotFoundException $e) {
            Log::error('Item not found: {message}', ['message' => $e->getMessage()]);
            exit();
        } catch (\Exception $e) {
            Log::critical(
                'Unhandled exception: {type} Message: {message} File: {file} Line: {line}',
                [
                    'type' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            );
            exit();
        }

        header_remove('Location');
        header_remove('Cache-Control');
        self::output($response);

    }

    public static function output(Response $response, ObjectifiedFunctions $funcs = null) {
        if (is_null($funcs)) {
            $funcs = new ObjectifiedFunctions();
        }

        $headers = $response->getHeaders();
        $content = $response->getContent();

        if (!isset($headers['ETag']) && !self::isIterable($content)) {
            $headers['ETag'] = self::generateETag($response);
        }
        if (!self::isIterable($content)) {
            $content = [$content];
        }

        $funcs->http_response_code($response->getCode());
        foreach ($headers as $name => $value) {
            $funcs->header($name . ': ' . $value);
        }

        $fp = fopen('php://output', 'wb');
        foreach ($content as $part) {
            fwrite($fp, $part);
        }
        fclose($fp);
    }

    private static function generateETag(Response $response) {
        return '"' . md5(http_build_query($response->getHeaders()) . "\0" . $response->getContent()) . '"';
    }

    private static function isIterable($thing) {
        return is_array($thing) || ($thing instanceof \Iterator) || ($thing instanceof \Generator);
    }

}
