<?php


namespace Kibo\Phast\Filters\HTML\ImagesOptimizationService;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\ValueObjects\Resource;

class ImageInliningManager {

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var int
     */
    private $maxImageInliningSize;

    /**
     * ImageInliningManager constructor.
     * @param Cache $cache
     * @param int $maxImageInliningSize
     */
    public function __construct(Cache $cache, $maxImageInliningSize) {
        $this->cache = $cache;
        $this->maxImageInliningSize = $maxImageInliningSize;
    }

    /**
     * @return int
     */
    public function getMaxImageInliningSize() {
        return $this->maxImageInliningSize;
    }

    public function canBeInlined(Resource $resource) {
        $size = $resource->getSize();
        return $size !== false
            && $size < $this->maxImageInliningSize
            && strpos($resource->getMimeType(), 'image/') === 0;
    }

    public function toDataUrl(Resource $resource) {
        return $resource->toDataURL();
    }
}
