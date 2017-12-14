<?php

namespace Kibo\Phast\Filters\Image;

use Kibo\Phast\Logging\LoggingTrait;

class CompositeImageFilter {
    use LoggingTrait;

    /**
     * @var ImageFilter[]
     */
    private $filters = [];

    public function addImageFilter(ImageFilter $filter) {
        $this->filters[] = $filter;
    }

    /**
     * @param Image $image
     * @param array $request
     * @return Image
     */
    public function apply(Image $image, array $request) {
        $filteredImage = $image;
        foreach ($this->filters as $filter) {
            $this->logger()->info('Applying {filter}', ['filter' => get_class($filter)]);
            $filteredImage = $filter->transformImage($filteredImage, $request);
        }
        if ($filteredImage->getSizeAsString() < $image->getSizeAsString()) {
            $this->logger()->info('Finished! Returning filtered image!');
            return $filteredImage;
        }
        $this->logger()->info('Finished, but filtered image is bigger than original! Returning original!');
        return $image;
    }

}
