<?php


namespace Kibo\Phast\Factories\Diagnostics\LogWriters;


use Kibo\Phast\Diagnostics\LogWriters\PHPErrorLogWriter;
use Kibo\Phast\Services\ServiceRequest;

class PHPErrorLogWriterFactory {

    public function make(array $config, ServiceRequest $request) {
        return new PHPErrorLogWriter($config);
    }

}
