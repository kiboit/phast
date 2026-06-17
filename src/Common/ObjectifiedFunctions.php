<?php

namespace Kibo\Phast\Common;

use Kibo\Phast\Exceptions\UndefinedObjectifiedFunction;

class ObjectifiedFunctions {
    /**
     * @var array<string, mixed>
     */
    private $functions = [];

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        $this->functions[$name] = $value;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function __get($name) {
        if (array_key_exists($name, $this->functions)) {
            return $this->functions[$name];
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name) {
        return isset($this->functions[$name]);
    }

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
