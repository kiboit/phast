<?php

namespace Kibo\Phast\Logging\LogWriters\Composite;

use Kibo\Phast\Logging\LogEntry;
use Kibo\Phast\Logging\LogWriter;
use Kibo\Phast\Logging\LogWriters\BaseLogWriter;

class Writer extends BaseLogWriter {
    /**
     * @var Writer[]
     */
    private $writers = [];

    public function addWriter(LogWriter $writer) {
        $this->writers[] = $writer;
    }

    protected function doWriteEntry(LogEntry $entry) {
        foreach ($this->writers as $writer) {
            $writer->writeEntry($entry);
        }
    }
}
