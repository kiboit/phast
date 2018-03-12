<?php


namespace Kibo\Phast\Filters\Service\Compression;


use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Exceptions\RuntimeException;
use Kibo\Phast\Services\ServiceFilter;
use Kibo\Phast\ValueObjects\Resource;

class DecompressingFilter implements ServiceFilter {

    /**
     * @var ObjectifiedFunctions
     */
    private $funcs;

    public function __construct(ObjectifiedFunctions $funcs = null) {
        $this->funcs = is_null($funcs) ? new ObjectifiedFunctions() : $funcs;
    }

    public function apply(Resource $resource, array $request) {
        if (!$this->funcs->function_exists('gzdecode')) {
            throw new RuntimeException('Function gzdecode() does not exist');
        }
        if (!$this->isCompressed($resource)  || $this->acceptsCompressed($request)) {
            return $resource;
        }
        return $resource->withContent(
            gzdecode($resource->getContent()),
            $resource->getMimeType(),
            'identity'
        );
    }

    private function isCompressed(Resource $resource) {
        return $resource->getEncoding() == 'gzip';
    }

    private function acceptsCompressed(array $request) {
        return count(array_intersect(['gzip', '*'], (array) @$request['accept-encoding'])) > 0;
    }


}
