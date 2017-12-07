<?php


namespace Kibo\Phast\Factories\Logging\LogWriters;


use Kibo\Phast\Services\ServiceRequest;

class LogWritersFactory {

    public function make(array $config, ServiceRequest $request) {
        $class = $config['class'];
        $factoryClass = str_replace('Kibo\Phast\\', 'Kibo\Phast\Factories\\', $class) . 'Factory';
        $writer = (new $factoryClass())->make($config, $request);
        if (isset ($config['levelMask'])) {
            $writer->setLevelMask($config['levelMask']);
        }
        return $writer;
    }

}
