<?php

namespace Kibo\Phast\Diagnostics\LogDrivers;

use Kibo\Phast\Diagnostics\LogEntry;

class JSONLFileLogWriter extends BaseLogWriter {

    /**
     * @var string
     */
    private $dir;

    /**
     * @var string
     */
    private $filename;

    /**
     * JSONLFileLogWriter constructor.
     * @param string $dir
     * @param string $suffix
     */
    public function __construct($dir, $suffix) {
        $this->dir = $dir;
        $suffix = preg_replace('/[^0-9A-Za-z_-]/', '', (string)$suffix);
        if (!empty ($suffix)) {
            $suffix = '-' . $suffix;
        }
        $this->filename = $this->dir . '/log' . $suffix . '.jsonl';
    }

    /**
     * @param LogEntry $entry
     */
    protected function doWriteEntry(LogEntry $entry) {
        $encoded = @json_encode($entry->toArray());
        if ($encoded) {
            $this->makeDirIfNotExists();
            @file_put_contents($this->filename, $encoded . "\n", FILE_APPEND | LOCK_EX);
        }
    }

    private function makeDirIfNotExists() {
        if (!@file_exists($this->dir)) {
            @mkdir($this->dir, 0777, true);
        }
    }
}
