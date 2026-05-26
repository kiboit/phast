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
        $cfFormat      = $this->config['images']['cloudflare-image-format'] ?? 'off';
        $behindCf      = $request->getHTTPRequest()->isCloudflare();
        $http          = $request->getHTTPRequest();
        $overrideCf    = $cfFormat !== 'off';

        if (!$behindCf || $overrideCf) {
            $params['varyAccept'] = true;

            $tryAvif = ($cfFormat === 'avif' || !$behindCf)
            && $this->serverSupportsAvif()
            && $this->browserSupportsAvif($http);

            $tryWebp = ($cfFormat === 'webp' || $cfFormat === 'avif' || !$behindCf)
            && $this->browserSupportsWebp($http);

            if ($tryAvif) {
                $params['preferredType'] = Image::TYPE_AVIF;
                Log::info('AVIF will be served if possible!');
            } elseif ($tryWebp) {
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
            && $resource->getMimeType() != Image::TYPE_AVIF
            && @$request['varyAccept']
        ) {
            $response->setHeader('Vary', 'Accept');
        }
        return $response;
    }

    private function serverSupportsAvif(): bool {
        // wp_image_editor_supports() already validates both WP editor class
        // AND underlying library (GD/Imagick), so no extra PHP version check needed here
        if (function_exists('wp_image_editor_supports')
            && wp_image_editor_supports(['mime_type' => 'image/avif'])
        ) {
            return true;
        }

        // Non-WordPress context: check the backend directly
        $usingImagick = class_exists('Imagick')
        && isset($this->config['images']['processor'])
        && $this->config['images']['processor'] === 'imagick';

        if ($usingImagick) {
            return in_array('AVIF', \Imagick::queryFormats());
        }

        // Raw GD check: imageavif() only exists on PHP 8.1+
        return PHP_VERSION_ID >= 80100 && function_exists('imageavif');
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
