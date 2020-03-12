<?php

namespace Kibo\Phast\Logging;

use Kibo\Phast\Logging\LogWriters\Dummy\Writer;
use Kibo\Phast\Logging\LogWriters\Factory;
use Kibo\Phast\Services\ServiceRequest;

class Log {
    /**
     * @var Logger
     */
    private static $logger;

    public static function setLogger(Logger $logger) {
        self::$logger = $logger;
    }

    public static function initWithDummy() {
        self::$logger = new Logger(new Writer());
    }

    public static function init(array $config, ServiceRequest $request, $service) {
        $writer = (new Factory())->make($config, $request);
        $logger = new Logger($writer);
        self::$logger = $logger->withContext([
            'documentRequestId' => $request->getDocumentRequestId(),
            'requestId' => mt_rand(0, 99999999),
            'service' => $service,
        ]);
    }

    /**
     * @return Logger
     */
    public static function get() {
        if (!isset(self::$logger)) {
            self::initWithDummy();
        }
        return self::$logger;
    }

    /**
     * @param array $context
     * @return Logger
     */
    public static function context(array $context) {
        return self::get()->withContext($context);
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
        self::get()->emergency($message, $context);
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
        self::get()->alert($message, $context);
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
        self::get()->critical($message, $context);
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
        self::get()->error($message, $context);
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
        self::get()->warning($message, $context);
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
        self::get()->notice($message, $context);
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
        self::get()->info($message, $context);
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
        self::get()->debug($message, $context);
    }
}
