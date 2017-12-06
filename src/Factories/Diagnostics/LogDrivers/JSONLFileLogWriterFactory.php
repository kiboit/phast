<?php


namespace Kibo\Phast\Factories\Diagnostics\LogDrivers;


use Kibo\Phast\Diagnostics\LogDrivers\JSONLFileLogWriter;
use Kibo\Phast\Services\ServiceRequest;

class JSONLFileLogWriterFactory {

    public function make(array $config, ServiceRequest $request) {
        return new JSONLFileLogWriter($config['logRoot'], $request->getRequestId());
    }

}
