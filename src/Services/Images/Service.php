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
        $params   = parent::getParams($request);
        $http     = $request->getHTTPRequest();
        $cfFormat = $this->config['images']['cloudflare-image-format'] ?? 'off';

        if ($this->proxySupportsAccept($http)) {
            // No Cloudflare proxy detected: format negotiated via Accept header as normal.
            $params['varyAccept'] = true;
            if ($this->serverSupportsAvif() && $this->browserSupportsAvif($http)) {
                $params['preferredType'] = Image::TYPE_AVIF;
                Log::info('AVIF will be served if possible!');
            } elseif ($this->browserSupportsWebp($http)) {
                $params['preferredType'] = Image::TYPE_WEBP;
                Log::info('WebP will be served if possible!');
            }

        } elseif ($cfFormat !== 'off') {
            // Cloudflare does not respect Vary: Accept - the first cached response
            // is served to all subsequent visitors regardless of their Accept header.
            // The Accept header is therefore not read, Vary: Accept is not set,
            // and the configured format is applied unconditionally to all requests.
            if ($cfFormat === 'avif' && $this->serverSupportsAvif()) {
                $params['preferredType'] = Image::TYPE_AVIF;
                Log::info('AVIF will be served unconditionally (Cloudflare mode).');
            } elseif ($cfFormat === 'webp' || ($cfFormat === 'avif' && !$this->serverSupportsAvif())) {
                $params['preferredType'] = Image::TYPE_WEBP;
                Log::info('WebP will be served unconditionally (Cloudflare mode).');
            }
            // varyAccept intentionally not set
        }
        // $cfFormat === 'off' behind Cloudflare: original format preserved, no action taken.

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

    private function browserSupportsAvif(Request $request): bool {
        return str_contains($request->getHeader('accept'), 'image/avif');
    }

    private function browserSupportsWebp(Request $request): bool {
        return str_contains($request->getHeader('accept'), 'image/webp');
    }

    private function proxySupportsAccept(Request $request) {
        return !$request->isCloudflare();
    }
}
