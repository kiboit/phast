<?php

namespace Kibo\Phast\Filters\Image\ImageImplementations;

abstract class BaseImage {

    /**
     * @var integer
     */
    protected $width;

    /**
     * @var integer
     */
    protected $height;

    /**
     * @var integer
     */
    protected $compression;

    /**
     * @param $width
     * @param $height
     * @return static
     */
    public function resize($width, $height) {
        $im = clone $this;
        $im->width = $width;
        $im->height = $height;
        return $im;
    }

    /**
     * @param $compression
     * @return static
     */
    public function compress($compression) {
        $im = clone $this;
        $im->compression = $compression;
        return $im;
    }
}
