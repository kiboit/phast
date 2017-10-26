<?php

namespace Kibo\Phast\Factories\Filters\HTML;

use Kibo\Phast\Filters\HTML\CompositeHTMLFilter;

class CompositeHTMLFilterFactory {


    public function make(array $config) {
        $composite = new CompositeHTMLFilter($config['documents']['maxBufferSizeToApply']);
        foreach (array_keys($config['documents']['filters']) as $class) {
            $filter = $this->makeFactory($class)->make($config);
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
