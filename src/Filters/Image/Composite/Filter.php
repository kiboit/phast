<?php

namespace Kibo\Phast\Filters\Image\Composite;

use Kibo\Phast\Filters\HTML\ImagesOptimizationService\ImageInliningManager;
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
     * @var ImageInliningManager
     */
    private $inliningManager;

    /**
     * @var ImageFilter[]
     */
    private $filters = [];

    /**
     * Filter constructor.
     * @param ImageFactory $imageFactory
     * @param ImageInliningManager $inliningManager
     */
    public function __construct(ImageFactory $imageFactory, ImageInliningManager $inliningManager) {
        $this->imageFactory = $imageFactory;
        $this->inliningManager = $inliningManager;
    }

    public function addImageFilter(ImageFilter $filter) {
        $this->filters[] = $filter;
    }

    public function getCacheSalt(Resource $resource, array $request) {
        $filters = array_map('get_class', $this->filters);
        $salts = array_map(function (ImageFilter $filter) use ($request) {
            return $filter->getCacheSalt($request);
        }, $this->filters);
        return implode("\n", array_merge(
            $filters,
            $salts,
            [$this->inliningManager->getMaxImageInliningSize(), $resource->getUrl(), $resource->getCacheSalt()]
        ));
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
                    'line' => $e->getLine(),
                ]);
            }
        }
        $sizeBefore = $filteredImage->getSizeAsString();
        $sizeAfter = $image->getSizeAsString();
        $sizeDifference = $sizeBefore - $sizeAfter;
        $this->logger()->info('Image processed. Size before/after: {sizeBefore}/{sizeAfter} ({sizeDifference})', [
            'sizeBefore' => $sizeBefore,
            'sizeAfter' => $sizeAfter,
            'sizeDifference' => $sizeDifference < 0 ? $sizeDifference : "+$sizeDifference",
        ]);
        if ($sizeDifference < 0) {
            $this->logger()->info('Return filtered image and save {sizeDifference} bytes', ['sizeDifference' => -$sizeDifference]);
            $image = $filteredImage;
        } else {
            $this->logger()->info('Return original image');
        }
        $processedResource = $resource->withContent($image->getAsString(), $image->getType());
        $this->inliningManager->maybeStoreForInlining($processedResource);
        return $processedResource;
    }
}
