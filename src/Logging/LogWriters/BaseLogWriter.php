<?php


namespace Kibo\Phast\Logging\LogWriters;

use Kibo\Phast\Logging\LogEntry;
use Kibo\Phast\Logging\LogWriter;

abstract class BaseLogWriter implements LogWriter {
    protected $levelMask = ~0;

    abstract protected function doWriteEntry(LogEntry $entry);

    public function setLevelMask($mask) {
        $this->levelMask = $mask;
    }

    public function writeEntry(LogEntry $entry) {
        if ($this->levelMask & $entry->getLevel()) {
            $this->doWriteEntry($entry);
        }
    }
}
