<?php

namespace Kibo\Phast\Services\Images;

use Kibo\Phast\Filters\Image\Image;
use Kibo\Phast\Logging\Log;
use Kibo\Phast\Services\BaseService;
use Kibo\Phast\Services\ServiceRequest;
use Kibo\Phast\ValueObjects\Resource;

class Service extends BaseService {

    protected function getParams(ServiceRequest $request) {
        $params = parent::getParams($request);
        if (strpos($request->getHTTPRequest()->getHeader('Accept'), 'image/webp') !== false) {
            $params['preferredType'] = Image::TYPE_WEBP;
            Log::info('WebP will be served if possible!');
        }
        return $params;
    }

    protected function makeResponse(Resource $resource, array $request) {
        $response = parent::makeResponse($resource, $request);
        $srcUrl = $resource->getUrl();
        $response->setHeader('Link', "<$srcUrl>; rel=\"canonical\"");
        $response->setHeader('Content-Type', $resource->getMimeType());
        $response->setHeader('ETag', md5($resource->getMimeType() . "\n" . $resource->getContent()));
        if ($resource->getMimeType() != Image::TYPE_PNG) {
            $response->setHeader('Vary', 'Accept');
        }
        return $response;
    }
}
