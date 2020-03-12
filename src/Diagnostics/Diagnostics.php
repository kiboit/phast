<?php


namespace Kibo\Phast\Diagnostics;

interface Diagnostics {
    /**
     * @param array $config
     */
    public function diagnose(array $config);
}
