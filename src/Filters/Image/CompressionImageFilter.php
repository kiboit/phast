<?php

namespace Kibo\Phast\Filters\Image;

use Kibo\Phast\Logging\LoggingTrait;

class CompressionImageFilter implements ImageFilter {
    use LoggingTrait;

    /**
     * @var array
     */
    private $compressions;

    /**
     * CompressionImageFilter constructor.
     *
     * @param array $compressions
     */
    public function __construct(array $compressions) {
        $this->compressions = $compressions;
    }

    public function transformImage(Image $image, array $request) {
        if (isset ($this->compressions[$image->getType()])) {
            $compression = $this->compressions[$image->getType()];
            $this->logger()->info(
                'Compressing {type} to {compression}',
                ['type' => $image->getType(), 'compression' => $compression]
            );
            return $image->compress($this->compressions[$image->getType()]);
        }
        $this->logger()->info('No compression level set for {type}', ['type' => $image->getType()]);
        return $image;
    }

}
