<?php


namespace Kibo\Phast\Filters\Service\Compression;

use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Exceptions\RuntimeException;
use Kibo\Phast\Filters\Service\CachedResultServiceFilter;
use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\ValueObjects\Resource;

class CompressingFilter implements CachedResultServiceFilter {
    use LoggingTrait;

    private $funcs;

    public function __construct(ObjectifiedFunctions $funcs = null) {
        $this->funcs = is_null($funcs) ? new ObjectifiedFunctions() : $funcs;
    }

    public function getCacheSalt(Resource $resource, array $request) {
        return $this->canApply() ? 'gzip' : 'identity';
    }

    public function apply(Resource $resource, array $request) {
        if (!$this->canApply()) {
            throw new RuntimeException('Function gzencode() does not exist');
        }

        $this->logger()->info('Compressing {url}', ['url' => (string) $resource->getUrl()]);
        return $resource->withContent(
            gzencode($resource->getContent()),
            null,
            'gzip'
        );
    }

    private function canApply() {
        return $this->funcs->function_exists('gzencode');
    }
}
