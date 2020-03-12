<?php

namespace Kibo\Phast\Filters\Image;

interface ImageFilter {
    /**
     * @param array $request
     * @return string
     */
    public function getCacheSalt(array $request);

    /**
     * @param Image $image
     * @param array $request
     * @return Image
     */
    public function transformImage(Image $image, array $request);
}
