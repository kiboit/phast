<?php

namespace Kibo\Phast\Filters\Image;

class CompositeImageFilter {

    /**
     * @var ImageFilter[]
     */
    private $filters = [];

    public function addFilter(ImageFilter $filter) {
        $this->filters[] = $filter;
    }

    public function apply(Image $image) {
        foreach ($this->filters as $filter) {
            $filter->transformImage($image);
        }
        $compressed = $image->getAsString();
        return strlen($compressed) < $image->getOriginalFileSize() ? $compressed : $image->getOriginalAsString();
    }

}
