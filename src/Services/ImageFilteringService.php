<?php

namespace Kibo\Phast\Services;

use Kibo\Phast\Factories\Filters\Image\ImageFactory;
use Kibo\Phast\Filters\Image\CompositeImageFilter;
use Kibo\Phast\Filters\Image\Image;
use Kibo\Phast\HTTP\Response;
use Kibo\Phast\Logging\Log;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\ValueObjects\URL;

class ImageFilteringService extends Service {

    /**
     * @var ImageFactory
     */
    private $imageFactory;

    /**
     * @var CompositeImageFilter
     */
    private $filter;

    /**
     * ImageFilteringService constructor.
     *
     * @param ServiceSignature $signature
     * @param string[] $whitelist
     * @param ImageFactory $imageFactory
     * @param CompositeImageFilter $filter
     */
    public function __construct(
        ServiceSignature $signature,
        array $whitelist,
        ImageFactory $imageFactory,
        CompositeImageFilter $filter
    ) {
        parent::__construct($signature, $whitelist);
        $this->imageFactory = $imageFactory;
        $this->filter = $filter;
    }

    protected function handle(array $request) {
        Log::info('Requested image is {src}', $request);
        $image = $this->imageFactory->getForURL(URL::fromString($request['src']));
        $image = $this->filter->apply($image, $request);

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

    protected function getParams(ServiceRequest $request) {
        $params = parent::getParams($request);
        if (strpos($request->getHTTPRequest()->getHeader('Accept'), 'image/webp') !== false) {
            $params['preferredType'] = Image::TYPE_WEBP;
            Log::info('WEBP will be served if possible!');
        }
        return $params;
    }
}
