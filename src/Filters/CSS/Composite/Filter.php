<?php


namespace Kibo\Phast\Filters\CSS\Composite;

use Kibo\Phast\Filters\CSS\CommentsRemoval;
use Kibo\Phast\Filters\Service\CachedResultServiceFilter;
use Kibo\Phast\Filters\Service\CompositeFilter;

class Filter extends CompositeFilter implements CachedResultServiceFilter {
    public function __construct() {
        $this->addFilter(new CommentsRemoval\Filter());
    }
}
