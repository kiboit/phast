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
        $httpRequest = $request->getHTTPRequest();
        if ($this->usesCloudflareConfiguredPreferredTypes($httpRequest)) {
            $preferredTypes = $this->getCloudflarePreferredTypes();
            if (!empty($preferredTypes)) {
                $params['preferredType'] = implode(',', $preferredTypes);
                Log::info('Preferred image types will be served if possible: {types}', [
                    'types' => $params['preferredType'],
                ]);
            }
        } elseif ($this->proxySupportsAccept($httpRequest)) {
            $params['varyAccept'] = true;
            $preferredTypes = $this->getBrowserSupportedPreferredTypes($httpRequest);
            if (!empty($preferredTypes)) {
                $params['preferredType'] = implode(',', $preferredTypes);
                Log::info('Preferred image types will be served if possible: {types}', [
                    'types' => $params['preferredType'],
                ]);
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

    private function getBrowserSupportedPreferredTypes(Request $request) {
        $accept = (string) $request->getHeader('accept');
        preg_match_all(
            '~(?:^|,)\s*(image/(?:avif|webp))\s*(?:;\s*(?!q\s*=\s*0(?:\.0*)?\s*(?=;|,|$))[^;,]*)*\s*(?=,|$)~i',
            $accept,
            $matches
        );
        $acceptedTypes = array_flip(array_map('strtolower', $matches[1]));
        return array_values(array_filter([Image::TYPE_AVIF, Image::TYPE_WEBP], function ($type) use ($acceptedTypes) {
            return isset($acceptedTypes[$type]);
        }));
    }

    private function getCloudflarePreferredTypes() {
        $format = strtolower(trim($this->config['images']['cloudflareImageFormat'] ?? 'webp'));
        if ($format === 'avif') {
            return [Image::TYPE_AVIF, Image::TYPE_WEBP];
        }
        if ($format === '') {
            return [];
        }
        return [Image::TYPE_WEBP];
    }

    private function proxySupportsAccept(Request $request) {
        return !empty($this->config['images']['cloudflareSupportsAcceptHeader'])
            || !$request->isCloudflare();
    }

    private function usesCloudflareConfiguredPreferredTypes(Request $request) {
        return $request->isCloudflare()
            && empty($this->config['images']['cloudflareSupportsAcceptHeader']);
    }
}
