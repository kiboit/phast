<?php


namespace Kibo\Phast\Filters\TextResources\Composite;

use Kibo\Phast\Filters\TextResources\TextResource;
use Kibo\Phast\Filters\TextResources\TextResourceFilter;
use Kibo\Phast\Logging\LoggingTrait;

class Filter implements TextResourceFilter {
    use LoggingTrait;

    protected $filters = [];

    public function addFilter(TextResourceFilter $filter) {
        $this->filters[] = $filter;
    }

    public function transform(TextResource $resource) {
        $this->logger()->info('Starting filtering for resource {url}', ['url' => $resource->getLocation()]);
        $result = array_reduce(
            $this->filters,
            function (TextResource $resource, TextResourceFilter $filter) {
                $this->logger()->info('Starting {filter}', ['filter' => get_class($filter)]);
                return $filter->transform($resource);
            },
            $resource
        );
        $this->logger()->info('Done filtering for resource {url}', ['url' => $resource->getLocation()]);
        return $result;
    }

}
