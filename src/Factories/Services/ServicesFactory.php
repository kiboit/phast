<?php

namespace Kibo\Phast\Factories\Services;

use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\Services\Service;

class ServicesFactory {

    /**
     * @param string $service
     * @param array $config
     * @return Service
     * @throws ItemNotFoundException
     */
    public function make($service, array $config) {
        if (!ctype_alpha($service)) {
            throw new ItemNotFoundException('Bad service');
        }
        $class = __NAMESPACE__ . '\\' . ucfirst($service) . 'ServiceFactory';
        if (class_exists($class)) {
            return (new $class())->make($config);
        }
        throw new ItemNotFoundException('Unknown service');
    }
}
