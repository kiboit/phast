<?php


namespace Kibo\Phast\Logging\LogWriters\JSONLFile;

use Kibo\Phast\Services\ServiceRequest;

class Factory {
    public function make(array $config, ServiceRequest $request) {
        return new \Kibo\Phast\Logging\LogWriters\JSONLFile\Writer($config['logRoot'], $request->getDocumentRequestId());
    }
}
