<?php

namespace Kibo\Phast\Common;


trait FactoryTrait {

    private function getFactoryClass($class, $suffix = '') {
        $factoryClass = preg_replace("/$suffix$/", 'Factory', $class);
        return $factoryClass;
    }

}
