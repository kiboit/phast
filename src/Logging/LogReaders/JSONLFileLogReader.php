<?php

namespace Kibo\Phast\Logging\LogReaders;

use Kibo\Phast\Logging\Common\JSONLFileLogTrait;
use Kibo\Phast\Logging\LogEntry;
use Kibo\Phast\Logging\LogReader;

class JSONLFileLogReader implements LogReader {
    use JSONLFileLogTrait;

    public function readEntries() {
        $fp = @fopen($this->filename, 'r');
        while ($fp && ($row = @fgets($fp))) {
            $decoded = @json_decode($row, true);
            if (!$decoded) {
                continue;
            }
            yield new LogEntry(@$decoded['level'], @$decoded['message'], @$decoded['context']);
        }
        @fclose($fp);
        @unlink($this->filename);
    }

    public function __destruct() {
        if (!($dir = @opendir($this->dir))) {
            return;
        }
        $tenMinutesAgo = time() - 600;
        while ($file = @readdir($dir)) {
            $filename = $this->dir . "/$file";
            if (preg_match('/\.jsonl$/', $file) && @filemtime($filename) < $tenMinutesAgo) {
                @unlink($filename);
            }
        }
    }


}
