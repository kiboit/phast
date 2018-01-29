<?php

namespace Kibo\Phast\Filters\Image\Composite;

use Kibo\Phast\Filters\Image\Exceptions\ImageProcessingException;
use Kibo\Phast\Filters\Image\ImageFactory;
use Kibo\Phast\Filters\Image\ImageFilter;
use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\Services\ServiceFilter;
use Kibo\Phast\ValueObjects\Resource;

class Filter implements ServiceFilter {
    use LoggingTrait;

    /**
     * @var ImageFactory
     */
    private $imageFactory;

    /**
     * @var ImageFilter[]
     */
    private $filters = [];

    /**
     * Filter constructor.
     * @param ImageFactory $imageFactory
     */
    public function __construct(ImageFactory $imageFactory) {
        $this->imageFactory = $imageFactory;
    }

    public function addImageFilter(ImageFilter $filter) {
        $this->filters[] = $filter;
    }

    public function getCacheHash(Resource $resource, array $request) {
        $lastModTime = $resource->getLastModificationTime();
        $filtersNames = array_map('get_class', $this->filters);
        sort($filtersNames);
        $key = array_merge([$lastModTime, (string)$resource->getUrl()], $filtersNames);
        if (isset ($request['width'])) {
            $key[] = $request['width'];
        }
        if (isset ($request['height'])) {
            $key[] = $request['height'];
        }
        if (isset ($request['preferredType'])) {
            $key[] = $request['preferredType'];
        }
        $key = implode("\n", $key);
        return $key;
    }


    /**
     * @param Resource $resource
     * @param array $request
     * @return Resource
     */
    public function apply(Resource $resource, array $request) {
        $image = $this->imageFactory->getForResource($resource);
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
            $image = $filteredImage;
        }
        $this->logger()->info('Finished, but filtered image is not smaller than original! Returning original!');
        return $resource->withContent($image->getAsString(), $image->getType());
    }

}
