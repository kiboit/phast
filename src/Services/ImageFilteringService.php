<?php

namespace Kibo\Phast\Services;

use Kibo\Phast\Factories\Filters\Image\CompositeImageFilterFactory;
use Kibo\Phast\Factories\Filters\Image\ImageFactory;
use Kibo\Phast\Filters\Image\Image;
use Kibo\Phast\HTTP\Request;
use Kibo\Phast\HTTP\Response;
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
     * @param string[] $whitelist
     */
    public function __construct(
        ImageFactory $imageFactory,
        CompositeImageFilterFactory $filterFactory,
        ServiceSignature $signature,
        array $whitelist
    ) {
        $this->imageFactory = $imageFactory;
        $this->filterFactory = $filterFactory;
        $this->signature = $signature;
        $this->whitelist = $whitelist;
    }

    protected function handle(array $request) {
        $image = $this->imageFactory->getForURL(URL::fromString($request['src']));
        $filter = $this->filterFactory->make($request);
        $image = $filter->apply($image);

        $response = new Response();
        $response->setHeader('Content-Type', $image->getType());
        $response->setHeader('Content-Length', $image->getSizeAsString());
        $response->setHeader('Cache-Control', 'max-age=' . (86400 * 365));
        $response->setHeader('ETag', md5($image->getType() . "\n" . $image->getAsString()));
        if ($image->getType() != Image::TYPE_PNG) {
            $response->setHeader('Vary', 'Accept');
        }
        $response->setContent($image->getAsString());

        return $response;
    }

    protected function getParams(Request $request) {
        $params = parent::getParams($request);
        if (strpos($request->getHeader('Accept'), 'image/webp') !== false) {
            $params['preferredType'] = Image::TYPE_WEBP;
        }
        return $params;
    }
}
