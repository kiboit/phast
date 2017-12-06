<?php

namespace Kibo\Phast\Diagnostics\LogDrivers;


use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Diagnostics\LogEntry;

class JSONLFileLogWriter extends BaseLogWriter {

    /**
     * @var ObjectifiedFunctions
     */
    private $funcs;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var resource
     */
    private $fp;

    /**
     * StreamLogWriter constructor.
     * @param $filename
     */
    public function __construct($filename) {
        $this->filename = $filename;
    }

    /**
     * @param LogEntry $entry
     */
    protected function doWriteEntry(LogEntry $entry) {
        $encoded = @json_encode($entry->toArray());
        if ($encoded) {
            @file_put_contents($this->filename, $encoded . "\n", FILE_APPEND | LOCK_EX);
        }
    }
}
