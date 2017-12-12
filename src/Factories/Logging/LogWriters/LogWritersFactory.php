<?php


namespace Kibo\Phast\Factories\Logging\LogWriters;


use Kibo\Phast\Logging\LogWriters\CompositeLogWriter;
use Kibo\Phast\Services\ServiceRequest;

class LogWritersFactory {

    public function make(array $config, ServiceRequest $request) {
        if (isset ($config['logWriters']) && count($config['logWriters']) > 1) {
            $class = CompositeLogWriter::class;
        } else if (isset ($config['logWriters'])) {
            $config = array_pop($config['logWriters']);
            $class = $config['class'];
        } else {
            $class = $config['class'];
        }
        $factoryClass = str_replace('Kibo\Phast\\', 'Kibo\Phast\Factories\\', $class) . 'Factory';
        $writer = (new $factoryClass())->make($config, $request);
        if (isset ($config['levelMask'])) {
            $writer->setLevelMask($config['levelMask']);
        }
        return $writer;
    }

}
