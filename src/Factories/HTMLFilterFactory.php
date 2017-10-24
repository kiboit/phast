<?php

namespace Kibo\Phast\Factories;

use Kibo\Phast\Filters\HTMLFilter;

interface HTMLFilterFactory {

    /**
     * @param array $config
     * @return HTMLFilter
     */
    public function make(array $config);

}
