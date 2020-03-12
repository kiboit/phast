<?php

namespace Kibo\Phast\Common;

use Kibo\Phast\Exceptions\UndefinedObjectifiedFunction;

class ObjectifiedFunctions {
    /**
     * @param string $name
     * @param array $arguments
     */
    public function __call($name, array $arguments) {
        if (isset($this->$name) && is_callable($this->$name)) {
            $fn = $this->$name;
            return $fn(...$arguments);
        }
        if (function_exists($name)) {
            return $name(...$arguments);
        }
        throw new UndefinedObjectifiedFunction("Undefined objectified function $name");
    }
}
