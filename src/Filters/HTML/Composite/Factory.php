<?php

namespace Kibo\Phast\Filters\HTML\Composite;

use Kibo\Phast\Environment\Package;
use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\ValueObjects\URL;

class Factory {
    use LoggingTrait;

    public function make(array $config) {
        $composite = new Filter(URL::fromString($config['documents']['baseUrl']), $config['outputServerSideStats']);
        foreach (array_keys($config['documents']['filters']) as $class) {
            $package = Package::fromPackageClass($class);
            if ($package->hasFactory()) {
                $filter = $package->getFactory()->make($config);
            } elseif (!class_exists($class)) {
                $this->logger(__METHOD__, __LINE__)
                     ->error("Skipping non-existent filter class: $class");
                continue;
            } else {
                $filter = new $class();
            }
            $composite->addHTMLFilter($filter);
        }
        return $composite;
    }
}
