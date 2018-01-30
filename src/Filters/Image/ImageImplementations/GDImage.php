<?php

namespace Kibo\Phast\Filters\Image\ImageImplementations;

use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\Filters\Image\Exceptions\ImageProcessingException;
use Kibo\Phast\Filters\Image\Image;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\ValueObjects\URL;

class GDImage extends BaseImage implements Image {

    /**
     * @var array
     */
    private static $outputCallbacks = [
        Image::TYPE_JPEG => 'imagejpeg',
        Image::TYPE_PNG  => 'imagepng',
        Image::TYPE_WEBP => 'imagewebp'
    ];

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
     * @var resource
     */
    private $gdImage;

    /**
     * @var ObjectifiedFunctions
     */
    private $funcs;

    /**
     * GDImage constructor.
     *
     * @param URL $imageURL
     * @param Retriever $retriever
     */
    public function __construct(URL $imageURL, Retriever $retriever, ObjectifiedFunctions $funcs = null) {
        $this->imageURL = $imageURL;
        $this->retriever = $retriever;
        $this->funcs = is_null($funcs) ? new ObjectifiedFunctions() : $funcs;
    }

    public function getWidth() {
        return isset ($this->width) ? $this->width : $this->getImageInfo()[0];
    }

    public function getHeight() {
        return isset ($this->height) ? $this->height : $this->getImageInfo()[1];
    }

    public function getType() {
        if (isset ($this->type)) {
            return $this->type;
        }
        $type = @image_type_to_mime_type($this->getImageInfo()[2]);
        if (!$type) {
            throw new ImageProcessingException('Could not determine image type');
        }
        return $type;
    }

    private function process() {
        try {
            if (isset ($this->width) && isset ($this->height)) {
                $gdCopy = @imagecreatetruecolor($this->width, $this->height);
                if (!$gdCopy) {
                    throw new ImageProcessingException('Failed to create new GD image for resizing');
                }
                @imagealphablending($gdCopy, false);
                if (!@imagecopyresampled($gdCopy, $this->gdImage, 0, 0, 0, 0,
                                         $this->width, $this->height,
                                         imagesx($this->gdImage), imagesy($this->gdImage))
                ) {
                    throw new ImageProcessingException('Failed to resample GD image');
                }
                @imagedestroy($this->gdImage);
                $this->gdImage = $gdCopy;
            }
            @imagesavealpha($this->gdImage, true);
            $callback = self::getOutputCallbackForType($this->getType());
            $tmpFile = @tempnam(sys_get_temp_dir(), 'Phast');
            if (!$tmpFile) {
                throw new ImageProcessingException('Could not create temporary file');
            }
            $tmpFh = @fopen($tmpFile, 'w');
            if (!$tmpFh) {
                throw new ImageProcessingException('Could not open temporary file');
            }
            if (isset ($this->compression)) {
                $ok = $callback($this->gdImage, $tmpFh, $this->compression);
            } else {
                $ok = $callback($this->gdImage, $tmpFh);
            }
            if (!$ok) {
                throw new ImageProcessingException('Could not write image to temporary file');
            }
            $string = file_get_contents($tmpFile);
            if ($string === false) {
                throw new ImageProcessingException('Could not read image from temporary file');
            }
            if ($callback == 'imagewebp' && strlen($string) % 2 == 1) {
                $string .= "\0";
            }
            return $string;
        } finally {
            if (isset ($this->gdImage) && $this->gdImage) {
                @imagedestroy($this->gdImage);
            }
            unset ($this->gdImage);
            if (isset ($tmpFh) && $tmpFh) {
                @fclose($tmpFh);
            }
            if (isset ($tmpFile) && $tmpFile) {
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
            $imageInfo = @getimagesizefromstring($this->getImageString());
            if ($imageInfo === false) {
                throw new ImageProcessingException('Could not read GD image info');
            }
            $this->imageInfo = $imageInfo;
        }
        return $this->imageInfo;
    }

    private function getImageString() {
        if (!isset ($this->imageString)) {
            $imageString = $this->retriever->retrieve($this->imageURL);
            if ($imageString === false) {
                throw new ItemNotFoundException('Could not find image: ' . $this->imageURL, 0, null, $this->imageURL);
            }
            $this->imageString = $imageString;
        }
        return $this->imageString;
    }

    public function resize($width, $height) {
        $this->loadGDImage();
        return parent::resize($width, $height);
    }

    public function compress($compression) {
        $this->loadGDImage();
        return parent::compress($compression);
    }

    public function encodeTo($type) {
        $callback = self::getOutputCallbackForType($type);
        $this->validateFunctionExists($callback);
        $this->loadGDImage();
        return parent::encodeTo($type);
    }

    private function loadGDImage() {
        $this->validateFunctionExists('imagecreatefromstring');
        if (isset ($this->gdImage) && @getimagesize($this->gdImage)) {
            return;
        }
        $gdImage = @imagecreatefromstring($this->getImageString());
        if ($gdImage === false) {
            throw new ImageProcessingException('Could not load GD image');
        }
        $this->gdImage = $gdImage;
    }

    private static function getOutputCallbackForType($type) {
        return isset (self::$outputCallbacks[$type])
               ? self::$outputCallbacks[$type]
               : self::$outputCallbacks[self::TYPE_PNG];
    }

    private function validateFunctionExists($function) {
        if (!$this->funcs->function_exists($function)) {
            throw new ImageProcessingException("Function $function() is missing!");
        }
    }

    protected function __clone() {
        $this->processed = false;
    }

}
