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
        $filteredImage = $image;
        try {
            foreach ($this->filters as $filter) {
                $filteredImage = $filter->transformImage($filteredImage);
            }
            return strlen($filteredImage->getAsString()) < strlen($image->getAsString()) ? $filteredImage : $image;
        } catch (\Exception $e) {
            return $image;
        }
    }

}
