<?php

namespace Kibo\Phast\Filters;

class ScriptsRearrangementTestComposite extends CompositeHTMLFilterTest {

    protected function getFilter() {
        return new ScriptsRearrangement();
    }

}
