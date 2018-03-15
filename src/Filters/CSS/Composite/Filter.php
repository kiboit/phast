<?php


namespace Kibo\Phast\Filters\CSS\Composite;

use Kibo\Phast\Filters\Service\CachedResultServiceFilter;
use Kibo\Phast\Filters\Service\CompositeFilter;
use Kibo\Phast\ValueObjects\Resource;
use Kibo\Phast\Filters\CSS\CommentsRemoval;

class Filter extends CompositeFilter implements CachedResultServiceFilter {

    /**
     * @var string
     */
    protected $serviceUrl;

    /**
     * Filter constructor.
     * @param string $serviceUrl
     */
    public function __construct($serviceUrl) {
        $this->serviceUrl = $serviceUrl;
        $this->filters[] = new CommentsRemoval\Filter();
    }

    public function getCacheHash(Resource $resource, array $request) {
        $parts = array_map('get_class', $this->filters);
        $parts[] = $this->serviceUrl;
        $parts[] = $resource->getUrl();
        $parts[] = md5($resource->getContent());
        if (isset ($request['strip-imports'])) {
            $parts[] = 'strip-imports';
        }
        return join("\n", $parts);
    }
}
