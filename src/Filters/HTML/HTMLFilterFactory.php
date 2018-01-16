<?php

namespace Kibo\Phast\Filters\HTML;

interface HTMLFilterFactory {

    /**
     * @param array $config
     * @return HTMLFilter
     */
    public function make(array $config);

}
