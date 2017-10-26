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

    /**
     * @var string
     */
    private $originalString;

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
     * @return int
     */
    public function getOriginalFileSize() {
        return strlen($this->originalString);
    }

    /**
     * @return string
     */
    public function getOriginalAsString() {
        return $this->originalString;
    }

    /**
     * @param string $originalString
     */
    public function setOriginalString($originalString) {
        $this->originalString = $originalString;
    }


}
