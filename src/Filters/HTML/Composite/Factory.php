<?php

namespace Kibo\Phast\Filters\HTML\Composite;

use Kibo\Phast\Common\FactoryTrait;
use Kibo\Phast\Filters\HTML\HTMLFilterFactory;

class Factory {
    use FactoryTrait;

    public function make(array $config) {
        $composite = new Filter($config['documents']['maxBufferSizeToApply']);
        foreach (array_keys($config['documents']['filters']) as $class) {
            $factory = $this->getFactoryClass($class,'Filter');
            if (class_exists($factory)) {
                $filter = $this->makeFactory($factory)->make($config);
            } else {
                $filter = new $class();
            }
            $composite->addHTMLFilter($filter);
        }
        return $composite;
    }

    /**
     * @param $class
     * @return HTMLFilterFactory
     */
    private function makeFactory($class) {
        return new $class();
    }

}
