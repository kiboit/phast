<?php


namespace Kibo\Phast\Filters\HTML\ImagesOptimizationService;

use Kibo\Phast\ValueObjects\Resource;

class ImageInliningManager {

    /**
     * @var int
     */
    private $maxImageInliningSize;

    /**
     * ImageInliningManager constructor.
     * @param int $maxImageInliningSize
     */
    public function __construct($maxImageInliningSize) {
        $this->maxImageInliningSize = $maxImageInliningSize;
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
