<?php


namespace Kibo\Phast\Factories\Logging\LogWriters;


use Kibo\Phast\Logging\LogWriters\JSONLFileLogWriter;
use Kibo\Phast\Services\ServiceRequest;

class JSONLFileLogWriterFactory {

    public function make(array $config, ServiceRequest $request) {
        return new JSONLFileLogWriter($config['logRoot'], $request->getDocumentRequestId());
    }

}
