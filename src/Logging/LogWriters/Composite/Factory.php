<?php


namespace Kibo\Phast\Logging\LogWriters\Composite;

use Kibo\Phast\Logging\LogWriters\Factory as WritersFactory;
use Kibo\Phast\Services\ServiceRequest;

class Factory {
    public function make(array $config, ServiceRequest $request) {
        $writer = new Writer();
        $factory = new WritersFactory();
        foreach ($config['logWriters'] as $writerConfig) {
            $writer->addWriter($factory->make($writerConfig, $request));
        }
        return $writer;
    }
}
