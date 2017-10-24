<?php

namespace Kibo\Phast;

use Kibo\Phast\Factories\CompositeHTMLFilterFactory;

class PhastDocumentFilters {

    public static function deploy(array $config) {
        $filter = (new CompositeHTMLFilterFactory())->make($config);
        ob_start([$filter, 'apply']);
    }

}
