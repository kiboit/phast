<?php

namespace Kibo\Phast\Factories;

use Kibo\Phast\Filters\CompositeHTMLFilter;

class CompositeHTMLFilterFactory {


    public function make(array $config) {
        $composite = new CompositeHTMLFilter($config['maxBufferSizeToApply']);
        foreach ($config['filters'] as $class => $filterConfig) {
            $filter = $this->makeFactory($class)->make($filterConfig);
            $composite->addHTMLFilter($filter);
        }
        return $composite;
    }

    /**
     * @param $filter
     * @return HTMLFilterFactory
     */
    private function makeFactory($filter) {
        $factory = str_replace('\Filters\\', '\Factories\\', $filter) . 'Factory';
        return new $factory();
    }

}
