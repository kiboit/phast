<?php

namespace Kibo\Phast\Filters\Image\Composite;

use Kibo\Phast\Exceptions\ImageProcessingException;
use Kibo\Phast\Filters\Image\Image;
use Kibo\Phast\Filters\Image\ImageFilter;
use Kibo\Phast\Logging\LoggingTrait;

class Filter {
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
            try {
                $filteredImage = $filter->transformImage($filteredImage, $request);
            } catch (ImageProcessingException $e) {
                $message = 'Image filter exception: Filter: {filter} Exception: {exceptionClass} Msg: {message} Code: {code} File: {file} Line: {line}';
                $this->logger()->critical($message, [
                    'filter' => get_class($filter),
                    'exceptionClass' => get_class($e),
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
            }
        }
        if ($filteredImage->getSizeAsString() < $image->getSizeAsString()) {
            $this->logger()->info('Finished! Returning filtered image!');
            return $filteredImage;
        }
        $this->logger()->info('Finished, but filtered image is bigger than original! Returning original!');
        return $image;
    }

}
