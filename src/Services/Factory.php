<?php

namespace Kibo\Phast\Services;

use Kibo\Phast\Exceptions\ItemNotFoundException;

class Factory {
    /**
     * @param string $service
     * @param array $config
     * @return BaseService
     * @throws ItemNotFoundException
     */
    public function make($service, array $config) {
        if (!preg_match('/^[a-z]+$/', $service)) {
            throw new ItemNotFoundException('Bad service');
        }
        $class = __NAMESPACE__ . '\\' . ucfirst($service) . '\\Factory';
        if (class_exists($class)) {
            return (new $class())->make($config);
        }
        throw new ItemNotFoundException('Unknown service');
    }
}
