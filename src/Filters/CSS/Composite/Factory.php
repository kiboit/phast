<?php


namespace Kibo\Phast\Filters\CSS\Composite;

use Kibo\Phast\Environment\Package;

class Factory {
    /**
     * @param array $config
     * @return Filter
     */
    public function make(array $config) {
        $class = \Kibo\Phast\Filters\HTML\ImagesOptimizationService\CSS\Filter::class;
        if (isset($config['documents']['filters'][$class]['serviceUrl'])) {
            $serviceUrl = $config['documents']['filters'][$class]['serviceUrl'];
        } else {
            $serviceUrl = $config['servicesUrl'] . '?service=images';
        }

        $filter = new Filter($serviceUrl);
        foreach (array_keys($config['styles']['filters']) as $filterClass) {
            $filter->addFilter(
                Package::fromPackageClass($filterClass)
                    ->getFactory()
                    ->make($config)
            );
        }
        return $filter;
    }
}
