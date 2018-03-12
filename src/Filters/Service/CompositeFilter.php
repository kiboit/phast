<?php


namespace Kibo\Phast\Filters\Service;


use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\Services\ServiceFilter;
use Kibo\Phast\ValueObjects\Resource;

class CompositeFilter implements ServiceFilter {
    use LoggingTrait;

    /**
     * @var ServiceFilter[]
     */
    protected $filters = [];

    public function addFilter(ServiceFilter $filter) {
        $this->filters[] = $filter;
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
