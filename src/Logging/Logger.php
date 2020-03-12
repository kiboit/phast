<?php

namespace Kibo\Phast\Logging;

use Kibo\Phast\Common\ObjectifiedFunctions;

class Logger {
    /**
     * @var LogWriter
     */
    private $writer;

    /**
     * @var array
     */
    private $context = [];

    /**
     * @var ObjectifiedFunctions
     */
    private $functions;

    /**
     * Logger constructor.
     * @param LogWriter $writer
     */
    public function __construct(LogWriter $writer, ObjectifiedFunctions $functions = null) {
        $this->writer = $writer;
        $this->functions = is_null($functions) ? new ObjectifiedFunctions() : $functions;
    }

    /**
     * Returns a new logger with default context
     * merged from the current logger and the passed array
     *
     * @param array $context
     * @return Logger
     */
    public function withContext(array $context) {
        $logger = clone $this;
        $logger->context = array_merge($this->context, $context);
        return $logger;
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function emergency($message, array $context = []) {
        $this->log(LogLevel::EMERGENCY, $message, $context);
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
    public function alert($message, array $context = []) {
        $this->log(LogLevel::ALERT, $message, $context);
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
    public function critical($message, array $context = []) {
        $this->log(LogLevel::CRITICAL, $message, $context);
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
    public function error($message, array $context = []) {
        $this->log(LogLevel::ERROR, $message, $context);
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
    public function warning($message, array $context = []) {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function notice($message, array $context = []) {
        $this->log(LogLevel::NOTICE, $message, $context);
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
    public function info($message, array $context = []) {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function debug($message, array $context = []) {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    protected function log($level, $message, array $context = []) {
        $context = array_merge(['timestamp' => $this->functions->microtime(true)], $context);
        $this->writer->writeEntry(new LogEntry($level, $message, array_merge($this->context, $context)));
    }
}
