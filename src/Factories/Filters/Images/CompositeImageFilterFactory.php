<?php

namespace Kibo\Phast\Factories\Filters\Images;

use Kibo\Phast\Filters\Image\CompositeImageFilter;

class CompositeImageFilterFactory {

    public function make(array $config, array $request) {
        $composite = new CompositeImageFilter();
        foreach ($config['filters'] as $class => $filterConfig) {
            $filter = $this->makeFactory($class)->make($filterConfig, $request);
            $composite->addImageFilter($filter);
        }
        return $composite;
    }

    private function makeFactory($filter) {
        $factory = str_replace('\Filters\\', '\Factories\Filters\\', $filter) . 'Factory';
        return new $factory();
    }

}
