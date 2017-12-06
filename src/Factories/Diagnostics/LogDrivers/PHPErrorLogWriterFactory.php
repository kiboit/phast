<?php


namespace Kibo\Phast\Factories\Diagnostics\LogDrivers;


use Kibo\Phast\Diagnostics\LogDrivers\PHPErrorLogWriter;
use Kibo\Phast\Services\ServiceRequest;

class PHPErrorLogWriterFactory {

    public function make(array $config, ServiceRequest $request) {
        return new PHPErrorLogWriter($config);
    }

}
