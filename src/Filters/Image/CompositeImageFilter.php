<?php

namespace Kibo\Phast\Filters\Image;

class CompositeImageFilter {

    /**
     * @var ImageFilter[]
     */
    private $filters = [];

    public function addImageFilter(ImageFilter $filter) {
        $this->filters[] = $filter;
    }

    public function apply(Image $image) {
        try {
            foreach ($this->filters as $filter) {
                $filter->transformImage($image);
            }
            $compressedImage = $image->transform();
            return strlen($compressedImage->getAsString()) < strlen($image->getAsString()) ? $compressedImage : $image;
        } catch (\Exception $e) {
            return $image;
        }
    }

}
