<?php


namespace Kibo\Phast\Factories\Logging\LogWriters;

use Kibo\Phast\Logging\LogWriters\CompositeLogWriter;
use Kibo\Phast\Services\ServiceRequest;

class CompositeLogWriterFactory {

    public function make(array $config, ServiceRequest $request) {
        $writer = new CompositeLogWriter();
        $factory = new LogWritersFactory();
        foreach ($config['logWriters'] as $writerConfig) {
            $writer->addWriter($factory->make($writerConfig, $request));
        }
        return $writer;
    }

}
