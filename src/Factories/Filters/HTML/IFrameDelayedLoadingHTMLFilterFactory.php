<?php

namespace Kibo\Phast\Factories\Filters\HTML;

use Kibo\Phast\Filters\HTML\IFrameDelayedLoadingHTMLFilter;

class IFrameDelayedLoadingHTMLFilterFactory implements HTMLFilterFactory {

    public function make(array $config) {
        return new IFrameDelayedLoadingHTMLFilter();
    }

}
