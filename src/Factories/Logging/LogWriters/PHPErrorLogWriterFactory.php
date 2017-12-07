<?php


namespace Kibo\Phast\Factories\Logging\LogWriters;


use Kibo\Phast\Logging\LogWriters\PHPErrorLogWriter;
use Kibo\Phast\Services\ServiceRequest;

class PHPErrorLogWriterFactory {

    public function make(array $config, ServiceRequest $request) {
        return new PHPErrorLogWriter($config);
    }

}
