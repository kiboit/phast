<?php

namespace Kibo\Phast\Filters\Image;

class CompressionImageFilter implements ImageFilter {

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
            return $image->compress($this->compressions[$image->getType()]);
        }

        return $image;
    }

}
