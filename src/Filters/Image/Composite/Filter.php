<?php

namespace Kibo\Phast\Filters\Image\Composite;

use Kibo\Phast\Filters\Image\Exceptions\ImageProcessingException;
use Kibo\Phast\Filters\Image\ImageFactory;
use Kibo\Phast\Filters\Image\ImageFilter;
use Kibo\Phast\Filters\Service\CachedResultServiceFilter;
use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\ValueObjects\Resource;

class Filter implements CachedResultServiceFilter {
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
        $filters = array_map('get_class', $this->filters);
        $salts = array_map(function (ImageFilter $filter) use ($request) {
            return $filter->getCacheSalt($request);
        }, $this->filters);
        $key = implode("\n", array_merge($filters, $salts, [$resource->getUrl(), $resource->getCacheSalt()]));
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
