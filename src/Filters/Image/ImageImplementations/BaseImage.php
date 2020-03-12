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
     * @var string
     */
    protected $type;

    /**
     * @return string
     */
    abstract public function getAsString();

    /**
     * @return integer
     */
    public function getSizeAsString() {
        return strlen($this->getAsString());
    }

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

    /**
     * @param $type
     * @return static
     */
    public function encodeTo($type) {
        $im = clone $this;
        $im->type = $type;
        return $im;
    }
}
