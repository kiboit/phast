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

    public function apply(Image $image, array $request) {
        $filteredImage = $image;
        foreach ($this->filters as $filter) {
            $filteredImage = $filter->transformImage($filteredImage, $request);
        }
        return $filteredImage->getSizeAsString() < $image->getSizeAsString() ? $filteredImage : $image;
    }

}
