<?php

namespace Kibo\Phast\Common;

use Kibo\Phast\Exceptions\UndefinedObjectifiedFunction;

class ObjectifiedFunctions {

    public function __call($name, $arguments) {
        if (isset ($this->$name) && is_callable($this->$name)) {
            return call_user_func_array($this->$name, $arguments);
        }
        if (function_exists($name)) {
            return call_user_func_array($name, $arguments);
        }
        throw new UndefinedObjectifiedFunction("Undefined objectified-function $name");
    }

}