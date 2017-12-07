<?php


namespace Kibo\Phast\Diagnostics\LogWriters;


use Kibo\Phast\Diagnostics\LogEntry;
use Kibo\Phast\Diagnostics\LogWriter;

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
