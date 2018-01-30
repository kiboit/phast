<?php


namespace Kibo\Phast\Filters\CSS\Composite;

use Kibo\Phast\Environment\Package;

class Factory {

    /**
     * @param array $config
     * @return Filter
     */
    public function make(array $config) {
        $filter = new Filter();
        foreach (array_keys($config['textResources']['filters']) as $filterClass) {
            $filter->addFilter(
                Package::fromPackageClass($filterClass)
                    ->getFactory()
                    ->make($config)
            );
        }
        return $filter;
    }

}
