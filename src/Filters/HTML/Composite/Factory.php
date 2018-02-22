<?php

namespace Kibo\Phast\Filters\HTML\Composite;

use Kibo\Phast\Environment\Package;
use Kibo\Phast\ValueObjects\URL;

class Factory {

    public function make(array $config) {
        $composite = new Filter(URL::fromString($config['documents']['baseUrl']));
        foreach (array_keys($config['documents']['filters']) as $class) {
            $package = Package::fromPackageClass($class);
            if ($package->hasFactory()) {
                $filter = $package->getFactory()->make($config);
            } else {
                $filter = new $class();
            }
            $composite->addHTMLFilter($filter);
        }
        return $composite;
    }

}
