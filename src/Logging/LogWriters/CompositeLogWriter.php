<?php

namespace Kibo\Phast\Logging\LogWriters;


use Kibo\Phast\Logging\LogEntry;
use Kibo\Phast\Logging\LogWriter;

class CompositeLogWriter extends BaseLogWriter {

    /**
     * @var LogWriter[]
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
