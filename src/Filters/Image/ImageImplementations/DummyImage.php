<?php

namespace Kibo\Phast\Filters\Image\ImageImplementations;

use Kibo\Phast\Filters\Image\Image;

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
     * @var string
     */
    private $type;

    /**
     * @var integer
     */
    private $compression;

    /**
     * DummyImage constructor.
     *
     * @param int $width
     * @param int $height
     */
    public function __construct($width = null, $height = null) {
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

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * @param int $compression
     * @return void
     */
    public function setCompression($compression) {
        $this->compression = $compression;
    }

    /**
     * @return int
     */
    public function getCompression() {
        return $this->compression;
    }

    public function getAsString() {
        return '';
    }

}
