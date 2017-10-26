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

    /**
     * @var resource
     */
    private $tmpFh;

    /**
     * @var resource
     */
    private $gdImage;

    public function __construct($imageString) {
        $this->imageString = $imageString;
    }

    public function getOriginalFileSize() {
        return strlen($this->imageString);
    }

    public function getOriginalAsString() {
        return $this->imageString;
    }

    public function getWidth() {
        return isset ($this->width) ? $this->width : $this->getImageInfo()[0];
    }

    public function getHeight() {
        return isset ($this->height) ? $this->height : $this->getImageInfo()[1];
    }

    public function setWidth($width) {
        $this->width = $width;
    }

    public function setHeight($height) {
        $this->height = $height;
    }

    public function getType() {
        $type = image_type_to_mime_type($this->getImageInfo()[2]);
        if ($type == 'image/jpeg') {
            return Image::TYPE_JPEG;
        }
        if ($type == 'image/png') {
            return Image::TYPE_PNG;
        }
        return Image::TYPE_OTHER;
    }

    public function setCompression($compression) {
        $this->compression = $compression;
    }

    public function getAsString() {
        $this->gdImage = @imagecreatefromstring($this->imageString);
        if ($this->gdImage === false) {
            $this->throwImageException('Could not load GD image');
        }
        if (isset ($this->width) && isset ($this->height)) {
            $this->gdImage = @imagescale($this->gdImage, $this->width, $this->height);
            if ($this->gdImage === false) {
                $this->throwImageException('Could not resize GD image');
            }
        }
        if ($this->getType() == Image::TYPE_PNG) {
            $callback = 'imagepng';
        } else if ($this->getType() == Image::TYPE_JPEG) {
            $callback = 'imagejpeg';
        } else {
            $callback = 'imagepng';
        }
        $this->tmpFh = tmpfile();
        if (!$this->tmpFh) {
            $this->throwImageException('Could not open temporary file');
        }
        if (isset ($this->compression)) {
            $ok = $callback($this->gdImage, $this->tmpFh, $this->compression);
        } else {
            $ok = $callback($this->gdImage, $this->tmpFh);
        }
        if (!$ok) {
            $this->throwImageException('Could not write image to temporary file');
        }
        $this->destroyGDImage();
        if (fseek($this->tmpFh, 0) !== 0) {
            $this->throwImageException('Could not seek to beginning of temporary image file');
        }
        $string = stream_get_contents($this->tmpFh);
        if ($string === false) {
            $this->throwImageException('Could not read image from temporary file');
        }
        $this->closeTmpFile();
        return $string;
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

    private function throwImageException($message) {
        $this->destroyGDImage();
        $this->closeTmpFile();
        throw new ImageException($message);
    }

    private function destroyGDImage() {
        if ($this->gdImage) {
            @imagedestroy($this->gdImage);
            $this->gdImage = null;
        }
    }

    private function closeTmpFile() {
        if ($this->tmpFh) {
            @fclose($this->tmpFh);
            $this->tmpFh = null;
        }
    }
}
