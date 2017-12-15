<?php


namespace Kibo\Phast\Logging\LogWriters;


use Kibo\Phast\Common\FactoryTrait;
use Kibo\Phast\Logging\LogWriters\Composite\Writer;
use Kibo\Phast\Services\ServiceRequest;

class Factory {
    use FactoryTrait;

    public function make(array $config, ServiceRequest $request) {
        if (isset ($config['logWriters']) && count($config['logWriters']) > 1) {
            $class = Writer::class;
        } else if (isset ($config['logWriters'])) {
            $config = array_pop($config['logWriters']);
            $class = $config['class'];
        } else {
            $class = $config['class'];
        }
        $factoryClass = $this->getFactoryClass($class, 'Writer');
        $writer = (new $factoryClass())->make($config, $request);
        if (isset ($config['levelMask'])) {
            $writer->setLevelMask($config['levelMask']);
        }
        return $writer;
    }

}
