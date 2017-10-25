<?php

namespace Kibo\Phast\Factories\Filters\HTML;

use Kibo\Phast\Filters\HTML\CompositeHTMLFilter;

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
        $factory = str_replace('\Filters\\', '\Factories\Filters\\', $filter) . 'Factory';
        return new $factory();
    }

}
