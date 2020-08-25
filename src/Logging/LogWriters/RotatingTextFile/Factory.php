<?php
namespace Kibo\Phast\Logging\LogWriters\RotatingTextFile;

use Kibo\Phast\Services\ServiceRequest;

class Factory {
    public function make(array $config, ServiceRequest $request) {
        return new Writer($config);
    }
}
