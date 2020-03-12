<?php


namespace Kibo\Phast\Logging\LogWriters\PHPError;

use Kibo\Phast\Services\ServiceRequest;

class Factory {
    public function make(array $config, ServiceRequest $request) {
        return new Writer($config);
    }
}
