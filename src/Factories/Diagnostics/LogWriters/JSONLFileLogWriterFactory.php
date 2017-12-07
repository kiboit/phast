<?php


namespace Kibo\Phast\Factories\Diagnostics\LogWriters;


use Kibo\Phast\Diagnostics\LogWriters\JSONLFileLogWriter;
use Kibo\Phast\Services\ServiceRequest;

class JSONLFileLogWriterFactory {

    public function make(array $config, ServiceRequest $request) {
        return new JSONLFileLogWriter($config['logRoot'], $request->getRequestId());
    }

}
