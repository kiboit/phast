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
     * @var string
     */
    private $imageString;

    private $transformationString;

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
     * @return int
     */
    public function getHeight() {
        return $this->height;
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
     * @return int
     */
    public function getCompression() {
        return $this->compression;
    }

    /**
     * @return string
     */
    public function getAsString() {
        return $this->imageString;
    }

    /**
     * @param string $imageString
     */
    public function setImageString($imageString) {
        $this->imageString = $imageString;
    }

    /**
     * @param int $width
     * @param int $height
     * @return DummyImage
     */
    public function resize($width, $height) {
        $im = clone $this;
        $im->width = $width;
        $im->height = $height;
        return $im;
    }

    /**
     * @param int $compression
     * @return DummyImage
     */
    public function compress($compression) {
        $im = clone $this;
        $im->compression = $compression;
        return $im;
    }

    /**
     * @param mixed $transformationString
     */
    public function setTransformationString($transformationString) {
        $this->transformationString = $transformationString;
    }

    /**
     * @return DummyImage
     */
    public function transform() {
        $im = clone $this;
        $im->imageString = $this->transformationString;
        return $im;
    }

}
