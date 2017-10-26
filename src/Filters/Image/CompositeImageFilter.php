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
            $compressed = $image->getAsString();
            return strlen($compressed) < $image->getOriginalFileSize() ? $compressed : $image->getOriginalAsString();
        } catch (\Exception $e) {
            return $image->getOriginalAsString();
        }
    }

}
