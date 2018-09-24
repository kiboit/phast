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
     * @var ObjectifiedFunctions
     */
    private $funcs;

    public function __construct(URL $imageURL, Retriever $retriever, ObjectifiedFunctions $funcs = null) {
        $this->imageURL = $imageURL;
        $this->retriever = $retriever;
        $this->funcs = is_null($funcs) ? new ObjectifiedFunctions() : $funcs;
    }

    public function getType() {
        throw new LogicException("No operations may be performed on DefaultImage");
    }

    public function getHeight() {
        throw new LogicException("No operations may be performed on DefaultImage");
    }

    public function getWidth() {
        throw new LogicException("No operations may be performed on DefaultImage");
    }

    public function getAsString() {
        return $this->getImageString();
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

    protected function __clone() {
        throw new LogicException("No operations may be performed on DefaultImage");
    }

}
