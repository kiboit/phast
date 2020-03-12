<?php


namespace Kibo\Phast\Logging\LogWriters\Dummy;

use Kibo\Phast\Logging\LogEntry;
use Kibo\Phast\Logging\LogWriter;

class Writer implements LogWriter {
    public function setLevelMask($mask) {
    }

    public function writeEntry(LogEntry $entry) {
    }
}
