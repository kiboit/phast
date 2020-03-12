<?php

namespace Kibo\Phast\Filters\Image\ImageImplementations;

use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\Exceptions\LogicException;
use Kibo\Phast\Filters\Image\Exceptions\ImageProcessingException;
use Kibo\Phast\Filters\Image\Image;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\ValueObjects\URL;

class DefaultImage extends BaseImage implements Image {
    /**
     * @var URL
     */
    private $imageURL;

    /**
     * @var Retriever
     */
    private $retriever;

    /**
     * @var string
     */
    private $imageString;

    /**
     * @var array
     */
    private $imageInfo;

    /**
     * @var ObjectifiedFunctions
     */
    private $funcs;

    public function __construct(URL $imageURL, Retriever $retriever, ObjectifiedFunctions $funcs = null) {
        $this->imageURL = $imageURL;
        $this->retriever = $retriever;
        $this->funcs = is_null($funcs) ? new ObjectifiedFunctions() : $funcs;
    }

    public function getWidth() {
        return isset($this->width) ? $this->width : $this->getImageInfo()[0];
    }

    public function getHeight() {
        return isset($this->height) ? $this->height : $this->getImageInfo()[1];
    }

    public function getType() {
        if (isset($this->type)) {
            return $this->type;
        }
        $type = @image_type_to_mime_type($this->getImageInfo()[2]);
        if (!$type) {
            throw new ImageProcessingException('Could not determine image type');
        }
        return $type;
    }

    public function getAsString() {
        return $this->getImageString();
    }

    private function getImageString() {
        if (!isset($this->imageString)) {
            $imageString = $this->retriever->retrieve($this->imageURL);
            if ($imageString === false) {
                throw new ItemNotFoundException('Could not find image: ' . $this->imageURL, 0, null, $this->imageURL);
            }
            $this->imageString = $imageString;
        }
        return $this->imageString;
    }

    private function getImageInfo() {
        if (!isset($this->imageInfo)) {
            if ($this->getImageString() === '') {
                throw new ImageProcessingException('Image is empty');
            }
            $imageInfo = @getimagesizefromstring($this->getImageString());
            if ($imageInfo === false) {
                throw new ImageProcessingException('Could not read GD image info');
            }
            $this->imageInfo = $imageInfo;
        }
        return $this->imageInfo;
    }

    protected function __clone() {
        throw new LogicException('No operations may be performed on DefaultImage');
    }
}
