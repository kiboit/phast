<?php

namespace Kibo\Phast\Diagnostics;


interface LogReader {

    /**
     * Reads LogMessage objects
     *
     * @return \Generator
     */
    public function readEntries();

}
