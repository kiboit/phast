<?php


namespace Kibo\Phast\Filters\Service;


use Kibo\Phast\Exceptions\RuntimeException;
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
                try {
                    return $filter->apply($resource, $request);
                } catch (RuntimeException $e) {
                    $message = 'Phast RuntimeException: Filter: {filter} Exception: {exceptionClass} Msg: {message} Code: {code} File: {file} Line: {line}';
                    $this->logger()->critical($message, [
                        'filter' => get_class($filter),
                        'exceptionClass' => get_class($e),
                        'message' => $e->getMessage(),
                        'code' => $e->getCode(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]);
                    return $resource;
                }
            },
            $resource
        );
        $this->logger()->info('Done filtering for resource {url}', ['url' => $resource->getUrl()]);
        return $result;
    }


}
