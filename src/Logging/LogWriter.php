<?php

namespace Kibo\Phast\Logging;

interface LogWriter {
    /**
     * Set a bit-mask to filter entries that are actually written
     *
     * @param int $mask
     * @return void
     */
    public function setLevelMask($mask);

    /**
     * Write an entry to the log
     *
     * @param LogEntry $entry
     * @return void
     */
    public function writeEntry(LogEntry $entry);
}
