<?php

namespace Kibo\Phast\Logging\LogWriters\JSONLFile;

use Kibo\Phast\Common\JSON;
use Kibo\Phast\Logging\Common\JSONLFileLogTrait;
use Kibo\Phast\Logging\LogEntry;
use Kibo\Phast\Logging\LogWriters\BaseLogWriter;

class Writer extends BaseLogWriter {
    use JSONLFileLogTrait;

    /**
     * @param LogEntry $entry
     */
    protected function doWriteEntry(LogEntry $entry) {
        $encoded = @JSON::encode($entry->toArray());
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
