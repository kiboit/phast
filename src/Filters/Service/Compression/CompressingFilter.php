<?php


namespace Kibo\Phast\Filters\Service\Compression;


use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Exceptions\RuntimeException;
use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\Services\ServiceFilter;
use Kibo\Phast\ValueObjects\Resource;

class CompressingFilter implements ServiceFilter {
    use LoggingTrait;

    private $funcs;

    public function __construct(ObjectifiedFunctions $funcs = null) {
        $this->funcs = is_null($funcs) ? new ObjectifiedFunctions() : $funcs;
    }

    public function apply(Resource $resource, array $request) {
        if (!$this->funcs->function_exists('gzencode')) {
            throw new RuntimeException('Function gzencode() does not exist');
        }

        $this->logger()->info('Compressing {url}', ['url' => (string) $resource->getUrl()]);
        return $resource->withContent(
            gzencode($resource->getContent()),
            null,
            'gzip'
        );
    }

}
