<?php

namespace Kibo\Phast\Filters\Image\ImageImplementations;

use Kibo\Phast\Exceptions\ImageException;
use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\Filters\Image\Image;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\ValueObjects\URL;

class GDImage extends BaseImage implements Image {

    /**
     * @var URL
     */
    private $imageURL;

    /**
     * @var Retriever
     */
    private $retriever;

    /**
     * @var bool
     */
    protected $processed = true;

    /**
     * @var string
     */
    private $imageString;

    /**
     * @var array
     */
    private $imageInfo;

    /**
     * GDImage constructor.
     *
     * @param URL $imageURL
     * @param Retriever $retriever
     */
    public function __construct(URL $imageURL, Retriever $retriever) {
        $this->imageURL = $imageURL;
        $this->retriever = $retriever;
    }

    public function getWidth() {
        return isset ($this->width) ? $this->width : $this->getImageInfo()[0];
    }

    public function getHeight() {
        return isset ($this->height) ? $this->height : $this->getImageInfo()[1];
    }

    public function getType() {
        $type = @image_type_to_mime_type($this->getImageInfo()[2]);
        if (!$type) {
            throw new ImageException('Could not determine image type');
        }
        return $type;
    }

    private function process() {
        try {
            $gdImage = @imagecreatefromstring($this->getImageString());
            if ($gdImage === false) {
                throw new ImageException('Could not load GD image');
            }
            if (isset ($this->width) && isset ($this->height)) {
                $gdCopy = @imagecreatetruecolor($this->width, $this->height);
                if (!$gdCopy) {
                    throw new ImageException('Failed to create new GD image for resizing');
                }
                @imagealphablending($gdCopy, false);
                if (!@imagecopyresampled($gdCopy, $gdImage, 0, 0, 0, 0,
                                         $this->width, $this->height,
                                         imagesx($gdImage), imagesy($gdImage))
                ) {
                    throw new ImageException('Failed to resample GD image');
                }
                @imagedestroy($gdImage);
                $gdImage = $gdCopy;
            }
            @imagesavealpha($gdImage, true);
            if ($this->getType() == Image::TYPE_JPEG) {
                $callback = 'imagejpeg';
            } else {
                $callback = 'imagepng';
            }
            $tmpFile = @tempnam(sys_get_temp_dir(), 'Phast');
            if (!$tmpFile) {
                throw new ImageException('Could not create temporary file');
            }
            $tmpFh = @fopen($tmpFile, 'w');
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
            $string = file_get_contents($tmpFile);
            if ($string === false) {
                throw new ImageException('Could not read image from temporary file');
            }
            return $string;
        } finally {
            if ($gdImage) {
                @imagedestroy($gdImage);
            }
            if ($tmpFh) {
                @fclose($tmpFh);
            }
            if ($tmpFile) {
                @unlink ($tmpFile);
            }
        }
    }

    public function getAsString() {
        if (!$this->processed) {
            $this->imageString = $this->process();
            $this->processed = true;
        }
        return $this->getImageString();
    }

    private function getImageInfo() {
        if (!isset ($this->imageInfo)) {
            $this->imageInfo = @getimagesizefromstring($this->getImageString());
            if ($this->imageInfo === false) {
                throw new ImageException('Could not read GD image info');
            }
        }
        return $this->imageInfo;
    }

    private function getImageString() {
        if (!isset ($this->imageString)) {
            $this->imageString = $this->retriever->retrieve($this->imageURL);
            if ($this->imageString === false) {
                throw new ItemNotFoundException('Could not find image: ' . $this->imageURL, 0, null, $this->imageURL);
            }
        }
        return $this->imageString;
    }

    protected function __clone() {
        $this->processed = false;
    }

}
