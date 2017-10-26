<?php

namespace Kibo\Phast;

use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\Exceptions\UnauthorizedException;
use Kibo\Phast\Factories\Filters\Image\CompositeImageFilterFactory;
use Kibo\Phast\Factories\Filters\Image\ImageFactory;
use Kibo\Phast\Security\ImagesOptimizationSignature;
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
     * @var ImagesOptimizationSignature
     */
    private $signature;

    /**
     * ImageFilteringService constructor.
     *
     * @param ImageFactory $imageFactory
     * @param CompositeImageFilterFactory $filterFactory
     * @param ImagesOptimizationSignature $signature
     */
    public function __construct(
        ImageFactory $imageFactory,
        CompositeImageFilterFactory $filterFactory,
        ImagesOptimizationSignature $signature
    ) {
        $this->imageFactory = $imageFactory;
        $this->filterFactory = $filterFactory;
        $this->signature = $signature;
    }

    public function serve(array $request) {
        $this->validateRequest($request);
        $image = $this->imageFactory->getForURL(URL::fromString($request['src']));
        $filter = $this->filterFactory->make($request);
        return $filter->apply($image);
    }

    private function validateRequest(array $request) {
        if (!isset ($request['src'])) {
            throw new ItemNotFoundException('No source is set!');
        }
        if (!isset ($request['token'])) {
            throw new UnauthorizedException();
        }
        $token = $request['token'];
        unset ($request['token']);
        if (!$this->signature->verify($token, http_build_query($request))) {
            throw new UnauthorizedException();
        }
    }

}
