<?php

namespace Kibo\Phast;

use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\Factories\Filters\Image\CompositeImageFilterFactory;
use Kibo\Phast\Factories\Filters\Image\ImageFactory;
use Kibo\Phast\ValueObjects\URL;

class ImageFilteringService {

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
     */
    public function __construct(ImageFactory $imageFactory, CompositeImageFilterFactory $filterFactory) {
        $this->imageFactory = $imageFactory;
        $this->filterFactory = $filterFactory;
    }

    public function serve(array $request) {
        if (!isset ($request['src'])) {
            throw new ItemNotFoundException('No source is set!');
        }
        $image = $this->imageFactory->getForURL(URL::fromString($request['src']));
        $filter = $this->filterFactory->make($request);
        return [$image->getType(), $filter->apply($image)];
    }

}
