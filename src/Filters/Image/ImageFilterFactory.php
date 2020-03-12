<?php


namespace Kibo\Phast\Filters\Image;

interface ImageFilterFactory {
    /**
     * @param array $config
     * @return ImageFilter
     */
    public function make(array $config);
}
