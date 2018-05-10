<?php


namespace Kibo\Phast\Filters\HTML\ImagesOptimizationService;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\ValueObjects\Resource;

class ImageInliningManager {
    use LoggingTrait;

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

    /**
     * @param Resource $resource
     * @return string|null
     */
    public function getUrlForInlining(Resource $resource) {
        if ($resource->getMimeType() !== 'image/svg+xml') {
            return $this->cache->get($this->getCacheKey($resource));
        }
        if ($this->hasSizeForInlining($resource)) {
            return $resource->toDataURL();
        }
        return null;
    }

    public function maybeStoreForInlining(Resource $resource) {
        if ($this->shouldStoreForInlining($resource)) {
            $this->logger()->info('Storing {url} for inlining', ['url' => $resource->getUrl()->toString()]);
            $this->cache->set($this->getCacheKey($resource), $resource->toDataURL());
        } else {
            $this->logger()->info('Not storing {url} for inlining', ['url' => $resource->getUrl()->toString()]);
        }
    }

    private function shouldStoreForInlining(Resource $resource) {
        return $this->hasSizeForInlining($resource)
            && strpos($resource->getMimeType(), 'image/') === 0
            && $resource->getMimeType() !== 'image/webp';
    }

    private function hasSizeForInlining(Resource $resource) {
        $size = $resource->getSize();
        return $size !== false
            && $size <= $this->maxImageInliningSize;
    }

    private function getCacheKey(Resource $resource) {
        return $resource->getUrl()->toString()
            . '|' . $resource->getCacheSalt()
            . '|' . $this->maxImageInliningSize;
    }
}
