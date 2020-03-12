<?php

namespace Kibo\Phast\Filters\HTML;

interface HTMLFilterFactory {
    /**
     * @param array $config
     * @return HTMLStreamFilter
     */
    public function make(array $config);
}
