<?php


namespace Kibo\Phast\Logging\LogWriters;

use Kibo\Phast\Environment\Package;
use Kibo\Phast\Logging\LogWriters\Composite\Writer;
use Kibo\Phast\Services\ServiceRequest;

class Factory {
    public function make(array $config, ServiceRequest $request) {
        if (isset($config['logWriters']) && count($config['logWriters']) > 1) {
            $class = Writer::class;
        } elseif (isset($config['logWriters'])) {
            $config = array_pop($config['logWriters']);
            $class = $config['class'];
        } else {
            $class = $config['class'];
        }
        $package = Package::fromPackageClass($class);
        $writer = $package->getFactory()->make($config, $request);
        if (isset($config['levelMask'])) {
            $writer->setLevelMask($config['levelMask']);
        }
        return $writer;
    }
}
