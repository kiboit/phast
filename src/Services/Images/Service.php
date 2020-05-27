<?php

namespace Kibo\Phast\Services\Images;

use Kibo\Phast\Filters\Image\Image;
use Kibo\Phast\HTTP\Request;
use Kibo\Phast\Logging\Log;
use Kibo\Phast\Services\BaseService;
use Kibo\Phast\Services\ServiceRequest;
use Kibo\Phast\ValueObjects\Resource;

class Service extends BaseService {
    protected function getParams(ServiceRequest $request) {
        $params = parent::getParams($request);
        if ($this->proxySupportsAccept($request->getHTTPRequest())) {
            $params['varyAccept'] = true;
            if ($this->browserSupportsWebp($request->getHTTPRequest())) {
                $params['preferredType'] = Image::TYPE_WEBP;
                Log::info('WebP will be served if possible!');
            }
        }
        return $params;
    }

    protected function makeResponse(Resource $resource, array $request) {
        $response = parent::makeResponse($resource, $request);
        $srcUrl = $resource->getUrl();
        $response->setHeader('Link', "<$srcUrl>; rel=\"canonical\"");
        $response->setHeader('Content-Type', $resource->getMimeType());
        if ($resource->getMimeType() != Image::TYPE_PNG
            && @$request['varyAccept']
        ) {
            $response->setHeader('Vary', 'Accept');
        }
        return $response;
    }

    protected function validateIntegrity(ServiceRequest $request) {
        if (!$this->config['images']['api-mode']) {
            parent::validateIntegrity($request);
        }
    }

    protected function validateWhitelisted(ServiceRequest $request) {
        if (!$this->config['images']['api-mode']) {
            parent::validateWhitelisted($request);
        }
    }

    private function browserSupportsWebp(Request $request) {
        return strpos($request->getHeader('accept'), 'image/webp') !== false;
    }

    private function proxySupportsAccept(Request $request) {
        return !$request->isCloudflare();
    }
}
