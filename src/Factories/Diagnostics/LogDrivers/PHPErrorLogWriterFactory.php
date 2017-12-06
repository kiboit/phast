<?php


namespace Kibo\Phast\Factories\Diagnostics\LogDrivers;


use Kibo\Phast\Diagnostics\LogDrivers\PHPErrorLogWriter;
use Kibo\Phast\HTTP\Request;

class PHPErrorLogWriterFactory {

    public function make(array $config, Request $request) {
        return new PHPErrorLogWriter($config);
    }

}
