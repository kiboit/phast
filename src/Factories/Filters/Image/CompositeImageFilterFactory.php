<?php

namespace Kibo\Phast\Factories\Filters\Image;

use Kibo\Phast\Filters\Image\CompositeImageFilter;

class CompositeImageFilterFactory {

    /**
     * @var array
     */
    private $config;

    /**
     * CompositeImageFilterFactory constructor.
     *
     * @param array $config
     */
    public function __construct(array $config) {
        $this->config = $config;
    }

    public function make(array $request) {
        $composite = new CompositeImageFilter();
        foreach ($this->config['filters'] as $class => $filterConfig) {
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
