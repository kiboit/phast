<?php

namespace Kibo\Phast\Logging\LogWriters;

use Kibo\Phast\Logging\Common\JSONLFileLogTrait;
use Kibo\Phast\Logging\LogEntry;

class JSONLFileLogWriter extends BaseLogWriter {
    use JSONLFileLogTrait;

    /**
     * @param LogEntry $entry
     */
    protected function doWriteEntry(LogEntry $entry) {
        $encoded = @json_encode($entry->toArray());
        if ($encoded) {
            $this->makeDirIfNotExists();
            @file_put_contents($this->filename, $encoded . "\n", FILE_APPEND | LOCK_EX);
        }
    }

    private function makeDirIfNotExists() {
        if (!@file_exists($this->dir)) {
            @mkdir($this->dir, 0777, true);
        }
    }
}
