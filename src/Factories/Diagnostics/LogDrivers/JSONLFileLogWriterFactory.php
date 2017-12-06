<?php


namespace Kibo\Phast\Factories\Diagnostics\LogDrivers;


use Kibo\Phast\Diagnostics\LogDrivers\JSONLFileLogWriter;
use Kibo\Phast\HTTP\Request;

class JSONLFileLogWriterFactory {

    public function make(array $config, Request $request) {
        // TODO: use a filename based on request id
        $filename = $config['logRoot'] . '/phast-log.jsonl';
        return new JSONLFileLogWriter($filename);
    }

}
