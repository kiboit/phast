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
use Kibo\Phast\ValueObjects\URL;

class PhastServices {
    /**
     * @param callable|null $getConfig
     */
    public static function serve(callable $getConfig = null) {
        $httpRequest = Request::fromGlobals();

        if ($httpRequest->getHeader('CDN-Loop')
            && preg_match('~(^|,)\s*Phast\b~', $httpRequest->getHeader('CDN-Loop'))
        ) {
            self::exitWithError(
                508,
                'Loop detected',
                '<p>Phast detected a request loop via the CDN-Loop header.</p>'
            );
        }

        $serviceRequest = ServiceRequest::fromHTTPRequest($httpRequest);
        $serviceParams = $serviceRequest->getParams();

        if (defined('PHAST_SERVICE')) {
            $service = PHAST_SERVICE;
        } elseif (!isset($serviceParams['service'])) {
            self::exitWithError(
                404,
                'Service parameter absent',
                '<p>Phast was not able to determine the request parameters. This might be because you are accessing the Phast service file directly without parameters, or because your server configuration causes the PATH_INFO environment variable to be missing.</p>' .
                '<p>This request has PATH_INFO set to: ' .
                (isset($_SERVER['PATH_INFO']) ? '"<code>' . self::escape($_SERVER['PATH_INFO']) . '</code>"' : '(none)') . '</p>' .
                '<p>This request has QUERY_STRING set to: ' .
                (isset($_SERVER['QUERY_STRING']) ? '"<code>' . self::escape($_SERVER['QUERY_STRING']) . '</code>"' : '(none)') . '</p>' .
                '<p>Either PATH_INFO or QUERY_STRING must contain the parameters for Phast contained in the URL. If the URL ends with parameters after a <code>/</code> character, those should end up in PATH_INFO. If the URL ends with parameters after a <code>?</code> character, those should end up in QUERY_STRING.</p>' .
                '<p>If the URL contains parameters, but those are not visible above, your server is misconfigured.</p>'
            );
        } else {
            $service = $serviceParams['service'];
        }

        $redirecting = false;
        if (!headers_sent()) {
            if (isset($serviceParams['src'])
                && !self::isRewrittenRequest($httpRequest)
                && self::isSafeRedirectDestination($serviceParams['src'], $httpRequest)
            ) {
                http_response_code(301);
                header('Location: ' . $serviceParams['src']);
                header('Cache-Control: max-age=86400');
                $redirecting = true;
            } else {
                http_response_code(500);
            }
        }

        if ($getConfig === null) {
            $config = [];
        } else {
            $config = $getConfig();
        }

        $userConfig = new Configuration($config);

        $runtimeConfig = Configuration::fromDefaults()
            ->withUserConfiguration($userConfig)
            ->withServiceRequest($serviceRequest)
            ->getRuntimeConfig()
            ->toArray();

        Log::init($runtimeConfig['logging'], $serviceRequest, $service);

        try {
            ServiceRequest::setDefaultSerializationMode($runtimeConfig['serviceRequestFormat']);

            Log::info('Starting service');
            $response = (new Factory())
                ->make($service, $runtimeConfig)
                ->serve($serviceRequest);
            Log::info('Service completed');
        } catch (UnauthorizedException $e) {
            if (!$redirecting) {
                http_response_code(403);
            }
            echo "Unauthorized\n";
            Log::error('Unauthorized exception: {message}', ['message' => $e->getMessage()]);
            exit();
        } catch (ItemNotFoundException $e) {
            if (!$redirecting) {
                http_response_code(404);
            }
            echo "Item not found\n";
            Log::error('Item not found: {message}', ['message' => $e->getMessage()]);
            exit();
        } catch (\Exception $e) {
            echo "Internal error, see logs\n";
            Log::critical(
                'Unhandled exception: {type} Message: {message} File: {file} Line: {line}',
                [
                    'type' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );
            exit();
        }

        header_remove('Location');
        header_remove('Cache-Control');
        self::output($httpRequest, $response, $runtimeConfig);
    }

    public static function isRewrittenRequest() {
        return !!ServiceRequest::getRewrittenService(Request::fromGlobals());
    }

    public static function output(
        Request $request,
        Response $response,
        array $config,
        ObjectifiedFunctions $funcs = null
    ) {
        if (is_null($funcs)) {
            $funcs = new ObjectifiedFunctions();
        }

        $headers = $response->getHeaders();
        $content = $response->getContent();

        if (!self::isIterable($content)) {
            $content = [$content];
        }

        $fp = fopen('php://output', 'wb');
        $zipping = false;
        if ($response->isCompressible()
            && self::shouldZip($request)
            && !empty($config['compressServiceResponse'])
        ) {
            $zipping = @$funcs->stream_filter_append(
                $fp,
                'zlib.deflate',
                STREAM_FILTER_WRITE,
                ['level' => 9, 'window' => 31]
            );
            if ($zipping) {
                $headers['Content-Encoding'] = 'gzip';
            }
        }

        $maxAge = 86400 * 365;

        $headers += [
            'Vary' => 'Accept-Encoding',
            'Cache-Control' => 'max-age=' . $maxAge,
            'Expires' => self::formatHeaderDate(time() + $maxAge),
            'X-Accel-Expires' => $maxAge,
            'Access-Control-Allow-Origin' => '*',
            'ETag' => self::generateETag($headers, $content),
            'Last-Modified' => self::formatHeaderDate(time()),
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'",
        ];

        if (is_array($content) && !$zipping) {
            $headers['Content-Length'] = (string) array_sum(array_map('strlen', $content));
        }

        $funcs->http_response_code($response->getCode());
        foreach ($headers as $name => $value) {
            $funcs->header($name . ': ' . $value);
        }
        foreach ($content as $part) {
            fwrite($fp, $part);
        }
        fclose($fp);
    }

    private static function formatHeaderDate($time) {
        return gmdate('D, d M Y H:i:s', $time) . ' GMT';
    }

    private static function shouldZip(Request $request) {
        return !$request->isCloudflare()
               && strpos($request->getHeader('Accept-Encoding'), 'gzip') !== false;
    }

    private static function generateETag(array $headers, $content) {
        $headersPart = http_build_query($headers);
        $contentPart = self::isIterable($content) ? uniqid() : $content;
        return '"' . md5($headersPart . "\0" . $contentPart) . '"';
    }

    private static function isIterable($thing) {
        return is_array($thing) || ($thing instanceof \Iterator) || ($thing instanceof \Generator);
    }

    private static function exitWithError($code, $title, $message) {
        http_response_code($code); ?>
        <!doctype html>
        <html>
        <head>
        <meta charset="utf-8">
        <title><?= self::escape($title); ?> &middot; Phast on <?= self::escape($_SERVER['SERVER_NAME']); ?></title>
        <style>
        html, body { min-height: 100%; }
        body { display: flex; flex-direction: column; align-items: center; }
        .container { max-width: 960px; }
        </style>
        </head>
        <body>
        <div class="container">
        <h1><?= self::escape($title); ?></h1>
        <?= $message; ?>
        <hr>
        Phast on <?= self::escape($_SERVER['SERVER_NAME']); ?>
        </div>
        </body>
        </html>
        <?php
        exit;
    }

    private static function escape($value) {
        return htmlentities((string) $value, ENT_QUOTES, 'UTF-8');
    }

    public static function isSafeRedirectDestination($url, Request $request) {
        $url = URL::fromString($url);
        if (!in_array($url->getScheme(), ['http', 'https'])) {
            return false;
        }
        $host = $url->getHost();
        if (!$host) {
            return false;
        }
        if ($host === $request->getHeader('Host')) {
            return true;
        }
        if (substr($host, -strlen($request->getHeader('Host')) - 1)
            === '.' . $request->getHeader('Host')
        ) {
            return true;
        }
        return false;
    }
}
