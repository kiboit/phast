<?php


namespace Kibo\Phast\Filters\CSS\Composite;

class Factory {

    /**
     * @param array $config
     * @return Filter
     */
    public function make(array $config) {
        $filter = new Filter();
        foreach (array_keys($config['textResources']['filters']) as $filterClass) {
            $filter->addFilter(new $filterClass);
        }
        return $filter;
    }

}
