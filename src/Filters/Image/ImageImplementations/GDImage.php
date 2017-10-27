<?php

namespace Kibo\Phast\Filters\Image\ImageImplementations;

use Kibo\Phast\Filters\Image\Image;
use Kibo\Phast\Exceptions\ImageException;

class GDImage implements Image {

    /**
     * @var string
     */
    private $imageString;

    /**
     * @var array
     */
    private $imageInfo;

    /**
     * @var integer
     */
    private $width;

    /**
     * @var integer
     */
    private $height;

    /**
     * @var integer
     */
    private $compression;

    public function __construct($imageString) {
        $this->imageString = $imageString;
    }

    public function getWidth() {
        return isset ($this->width) ? $this->width : $this->getImageInfo()[0];
    }

    public function getHeight() {
        return isset ($this->height) ? $this->height : $this->getImageInfo()[1];
    }

    public function resize($width, $height) {
        $im = clone $this;
        $im->width = $width;
        $im->height = $height;
        return $im;
    }

    public function compress($compression) {
        $im = clone $this;
        $im->compression = $compression;
        return $im;
    }

    public function getType() {
        $type = @image_type_to_mime_type($this->getImageInfo()[2]);
        if (!$type) {
            throw new ImageException('Could not determine image type');
        }
        return $type;
    }

    public function transform() {
        try {
            $gdImage = @imagecreatefromstring($this->imageString);
            if ($gdImage === false) {
                throw new ImageException('Could not load GD image');
            }
            if (isset ($this->width) && isset ($this->height)) {
                $gdImage = @imagescale($gdImage, $this->width, $this->height, IMG_BICUBIC);
                if ($gdImage === false) {
                    throw new ImageException('Could not resize GD image');
                }
            }
            @imagesavealpha($gdImage, true);
            if ($this->getType() == Image::TYPE_JPEG) {
                $callback = 'imagejpeg';
            } else {
                $callback = 'imagepng';
            }
            $tmpFh = @fopen('php://memory', 'w+');
            if (!$tmpFh) {
                throw new ImageException('Could not open temporary file');
            }
            if (isset ($this->compression)) {
                $ok = $callback($gdImage, $tmpFh, $this->compression);
            } else {
                $ok = $callback($gdImage, $tmpFh);
            }
            if (!$ok) {
                throw new ImageException('Could not write image to temporary file');
            }
            if (fseek($tmpFh, 0) !== 0) {
                throw new ImageException('Could not seek to beginning of temporary image file');
            }
            $string = stream_get_contents($tmpFh);
            if ($string === false) {
                throw new ImageException('Could not read image from temporary file');
            }
            return new self($string);
        } finally {
            if (isset ($gdImage)) {
                @imagedestroy($gdImage);
            }
            if (isset ($tmpFh)) {
                @fclose($tmpFh);
            }
        }
    }

    public function getAsString() {
        return $this->imageString;
    }

    private function getImageInfo() {
        if (!isset ($this->imageInfo)) {
            $this->imageInfo = @getimagesizefromstring($this->imageString);
            if ($this->imageInfo === false) {
                throw new ImageException('Could not read GD image info');
            }
        }
        return $this->imageInfo;
    }
}
