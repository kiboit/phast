<?php

namespace Kibo\Phast\Factories\Filters\HTML;

use Kibo\Phast\Filters\HTML\HTMLFilter;

interface HTMLFilterFactory {

    /**
     * @param array $config
     * @return HTMLFilter
     */
    public function make(array $config);

}
