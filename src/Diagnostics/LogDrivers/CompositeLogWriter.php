<?php

namespace Kibo\Phast\Diagnostics\LogDrivers;


use Kibo\Phast\Diagnostics\LogEntry;
use Kibo\Phast\Diagnostics\LogWriter;

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
