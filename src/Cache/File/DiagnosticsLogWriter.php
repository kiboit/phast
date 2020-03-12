<?php


namespace Kibo\Phast\Cache\File;

use Kibo\Phast\Exceptions\RuntimeException;
use Kibo\Phast\Logging\LogEntry;
use Kibo\Phast\Logging\LogWriter;

class DiagnosticsLogWriter implements LogWriter {
    public function setLevelMask($mask) {
    }

    public function writeEntry(LogEntry $entry) {
        if ($entry->getLevel() > 2) {
            $needles = array_map(function ($key) {
                return '{' . $key . '}';
            }, array_keys($entry->getContext()));
            $message = str_replace($needles, $entry->getContext(), $entry->getMessage());
            throw new RuntimeException("Error: Level: {$entry->getLevel()}, Msg: $message");
        }
    }
}
