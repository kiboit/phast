<?php

namespace Kibo\Phast\Filters\Image;

class DummyImage implements Image {

    /**
     * @var integer
     */
    private $width;

    /**
     * @var integer
     */
    private $height;

    /**
     * DummyImage constructor.
     *
     * @param int $width
     * @param int $height
     */
    public function __construct($width, $height) {
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * @return int
     */
    public function getWidth() {
        return $this->width;
    }

    /**
     * @param int $width
     */
    public function setWidth($width) {
        $this->width = $width;
    }

    /**
     * @return int
     */
    public function getHeight() {
        return $this->height;
    }

    /**
     * @param int $height
     */
    public function setHeight($height) {
        $this->height = $height;
    }



}
