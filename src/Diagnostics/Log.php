<?php

namespace Kibo\Phast\Diagnostics;


use Kibo\Phast\Factories\Diagnostics\LogWriters\LogWritersFactory;
use Kibo\Phast\Services\ServiceRequest;

class Log {

    /**
     * @var Logger
     */
    private static $logger;

    public static function init(array $config, ServiceRequest $request, $service) {
        $writer = (new LogWritersFactory())->make($config, $request);
        $logger = new Logger($writer);
        self::$logger = $logger->withContext([
            'requestId' => $request->getRequestId(),
            'service' => $service
        ]);
    }

    /**
     * @param array $context
     * @return Logger
     */
    public static function context(array $context) {
        return self::$logger->withContext($context);
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function emergency($message, array $context = []) {
        self::$logger->emergency($message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function alert($message, array $context = []) {
        self::$logger->alert($message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function critical($message, array $context = []) {
        self::$logger->critical($message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function error($message, array $context = []) {
        self::$logger->error($message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function warning($message, array $context = []) {
        self::$logger->warning($message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function notice($message, array $context = []) {
        self::$logger->notice($message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function info($message, array $context = []) {
        self::$logger->info($message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function debug($message, array $context = []) {
        self::$logger->debug($message, $context);
    }

}
