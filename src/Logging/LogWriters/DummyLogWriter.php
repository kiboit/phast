<?php


namespace Kibo\Phast\Logging\LogWriters;


use Kibo\Phast\Logging\LogEntry;
use Kibo\Phast\Logging\LogWriter;

class DummyLogWriter implements LogWriter {

    public function setLevelMask($mask) {}

    public function writeEntry(LogEntry $entry) {}

}
