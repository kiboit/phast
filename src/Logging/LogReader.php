<?php

namespace Kibo\Phast\Logging;

interface LogReader {
    /**
     * Reads LogMessage objects
     *
     * @return \Generator
     */
    public function readEntries();
}
