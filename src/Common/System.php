<?php

namespace Kibo\Phast\Common;

use Kibo\Phast\Exceptions\UndefinedObjectifiedFunction;

class System {
    private $functions;

    public function __construct(ObjectifiedFunctions $functions = null) {
        if ($functions === null) {
            $functions = new ObjectifiedFunctions();
        }

        $this->functions = $functions;
    }

    public function getUserId() {
        try {
            return (int) $this->functions->posix_geteuid();
        } catch (UndefinedObjectifiedFunction $e) {
            return 0;
        }
    }
}
