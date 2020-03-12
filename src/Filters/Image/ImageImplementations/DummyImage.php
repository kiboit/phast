<?php

namespace Kibo\Phast\Filters\Image\ImageImplementations;

use Kibo\Phast\Filters\Image\Image;

class DummyImage extends BaseImage implements Image {
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
     * @param mixed $transformationString
     */
    public function setTransformationString($transformationString) {
        $this->transformationString = $transformationString;
    }

    protected function __clone() {
        $this->imageString = $this->transformationString;
    }
}
