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
        if (!$encoded) {
            return;
        }
        $this->makeDirIfNotExists();
        if (!($fp = $this->openLogFile(true))) {
            return;
        }
        if (@flock($fp, LOCK_EX)) {
            @fseek($fp, 0, SEEK_END);
            @fwrite($fp, $encoded . "\n");
            @fflush($fp);
            @flock($fp, LOCK_UN);
        }
        @fclose($fp);
    }

    private function makeDirIfNotExists() {
        if (!@file_exists($this->dir)) {
            @mkdir($this->dir, 0777, true);
        }
    }
}
