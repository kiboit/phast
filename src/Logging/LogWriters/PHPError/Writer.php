<?php

namespace Kibo\Phast\Logging\LogWriters\PHPError;

use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Logging\LogEntry;
use Kibo\Phast\Logging\LogWriters\BaseLogWriter;

class Writer extends BaseLogWriter {
    private $messageType = 0;

    private $destination = null;

    private $extraHeaders = null;

    /**
     * @var ObjectifiedFunctions
     */
    private $funcs;

    /**
     * PHPErrorLogWriter constructor.
     * @param array $config
     * @param ObjectifiedFunctions $funcs
     */
    public function __construct(array $config, ObjectifiedFunctions $funcs = null) {
        foreach (['messageType', 'destination', 'extraHeaders'] as $field) {
            if (isset($config[$field])) {
                $this->$field = $config[$field];
            }
        }
        $this->funcs = is_null($funcs) ? new ObjectifiedFunctions() : $funcs;
    }

    protected function doWriteEntry(LogEntry $entry) {
        if ($this->levelMask & $entry->getLevel()) {
            $this->funcs->error_log(
                $this->interpolate($entry->getMessage(), $entry->getContext()),
                $this->messageType,
                $this->destination,
                $this->extraHeaders
            );
        }
    }

    private function interpolate($message, $context) {
        $prefix = '';
        $prefixKeys = [
            'requestId',
            'service',
            'class',
            'method',
            'line',
        ];
        foreach ($prefixKeys as $key) {
            if (isset($context[$key])) {
                $prefix .= '{' . $key . "}\t";
            }
        }
        return preg_replace_callback('/{(.+?)}/', function ($match) use ($context) {
            return array_key_exists($match[1], $context) ? $context[$match[1]] : $match[0];
        }, $prefix . $message);
    }
}
