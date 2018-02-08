<?php


namespace Kibo\Phast\Filters\CSS\Composite;

use Kibo\Phast\Filters\Service\CachedResultServiceFilter;
use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\Services\ServiceFilter;
use Kibo\Phast\ValueObjects\Resource;
use Kibo\Phast\Filters\CSS\CommentsRemoval;

class Filter implements CachedResultServiceFilter {
    use LoggingTrait;

    protected $filters = [];

    public function __construct() {
        $this->filters[] = new CommentsRemoval\Filter();
    }

    public function addFilter(ServiceFilter $filter) {
        $this->filters[] = $filter;
    }

    public function getCacheHash(Resource $resource, array $request) {
        $parts = array_map('get_class', $this->filters);
        $parts[] = md5($resource->getContent());
        if (isset ($request['strip-imports'])) {
            $parts[] = 'strip-imports';
        }
        return join("\n", $parts);
    }

    public function apply(Resource $resource, array $request) {
        $this->logger()->info('Starting filtering for resource {url}', ['url' => $resource->getUrl()]);
        $result = array_reduce(
            $this->filters,
            function (Resource $resource, ServiceFilter $filter) use ($request) {
                $this->logger()->info('Starting {filter}', ['filter' => get_class($filter)]);
                return $filter->apply($resource, $request);
            },
            $resource
        );
        $this->logger()->info('Done filtering for resource {url}', ['url' => $resource->getUrl()]);
        return $result;
    }

}
