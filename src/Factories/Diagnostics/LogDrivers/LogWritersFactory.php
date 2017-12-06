<?php


namespace Kibo\Phast\Factories\Diagnostics\LogDrivers;


use Kibo\Phast\HTTP\Request;

class LogWritersFactory {

    public function make(array $config, Request $request) {
        $class = $config['class'];
        $factoryClass = str_replace('Kibo\Phast\\', 'Kibo\Phast\Factories\\', $class) . 'Factory';
        // TODO: set the logging level on the filter before we return it
        return (new $factoryClass())->make($config, $request);
    }

}
