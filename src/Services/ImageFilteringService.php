<?php

namespace Kibo\Phast\Services;

use Kibo\Phast\Factories\Filters\Image\CompositeImageFilterFactory;
use Kibo\Phast\Factories\Filters\Image\ImageFactory;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\ValueObjects\URL;

class ImageFilteringService extends Service {

    /**
     * @var ImageFactory
     */
    private $imageFactory;

    /**
     * @var CompositeImageFilterFactory
     */
    private $filterFactory;

    /**
     * ImageFilteringService constructor.
     *
     * @param ImageFactory $imageFactory
     * @param CompositeImageFilterFactory $filterFactory
     * @param ServiceSignature $signature
     */
    public function __construct(
        ImageFactory $imageFactory,
        CompositeImageFilterFactory $filterFactory,
        ServiceSignature $signature
    ) {
        $this->imageFactory = $imageFactory;
        $this->filterFactory = $filterFactory;
        $this->signature = $signature;
    }

    protected function handle(array $request) {
        $image = $this->imageFactory->getForURL(URL::fromString($request['src']));
        $filter = $this->filterFactory->make($request);
        return $filter->apply($image);
    }
}
