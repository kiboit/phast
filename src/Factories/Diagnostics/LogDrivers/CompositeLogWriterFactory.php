<?php


namespace Kibo\Phast\Factories\Diagnostics\LogDrivers;

use Kibo\Phast\Diagnostics\LogDrivers\CompositeLogWriter;
use Kibo\Phast\HTTP\Request;

class CompositeLogWriterFactory {

    public function make(array $config, Request $request) {
        $writer = new CompositeLogWriter();
        $factory = new LogWritersFactory();
        foreach ($config['logWriters'] as $writerConfig) {
            $writer->addWriter($factory->make($writerConfig, $request));
        }
        return $writer;
    }

}
