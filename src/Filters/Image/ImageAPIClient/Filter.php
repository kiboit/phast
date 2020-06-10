<?php

namespace Kibo\Phast\Filters\Image\ImageAPIClient;

use Kibo\Phast\Filters\Image\Exceptions\ImageProcessingException;
use Kibo\Phast\Filters\Image\Image;
use Kibo\Phast\Filters\Image\ImageFilter;
use Kibo\Phast\Filters\Image\ImageImplementations\DummyImage;
use Kibo\Phast\HTTP\Client;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\Services\ServiceRequest;
use Kibo\Phast\ValueObjects\URL;

class Filter implements ImageFilter {
    /**
     * @var array
     */
    private $config;

    /**
     * @var ServiceSignature
     */
    private $signature;

    /**
     * @var Client
     */
    private $client;

    /**
     * Filter constructor.
     * @param array $config
     * @param ServiceSignature $signature
     * @param Client $client
     */
    public function __construct(array $config, ServiceSignature $signature, Client $client) {
        $this->config = $config;
        $this->signature = $signature;
        $this->client = $client;
        $this->signature->setIdentities('');
    }

    public function getCacheSalt(array $request) {
        $result = 'api-call';
        foreach (['width', 'height', 'preferredType'] as $key) {
            if (isset($request[$key])) {
                $result .= "-$key-{$request[$key]}";
            }
        }
        return $result;
    }

    public function transformImage(Image $image, array $request) {
        $url = $this->getRequestURL($request);
        $headers = $this->getRequestHeaders($image, $request);
        $data = $image->getAsString();
        try {
            $response = $this->client->post(URL::fromString($url), $data, $headers);
        } catch (\Exception $e) {
            throw new ImageProcessingException(
                'Request exception: ' . get_class($e)
                . ' MSG: ' . $e->getMessage()
                . ' Code: ' . $e->getCode()
            );
        }
        if (strlen($response->getContent()) === 0) {
            throw new ImageProcessingException('Image API response is empty');
        }
        $newImage = new DummyImage();
        $newImage->setImageString($response->getContent());
        $headers = [];
        foreach ($response->getHeaders() as $name => $value) {
            $headers[strtolower($name)] = $value;
        }
        $newImage->setType($headers['content-type']);
        return $newImage;
    }

    private function getRequestURL(array $request) {
        $params = [];
        foreach (['width', 'height'] as $key) {
            if (isset($request[$key])) {
                $params[$key] = $request[$key];
            }
        }
        return (new ServiceRequest())
            ->withUrl(URL::fromString($this->config['api-url']))
            ->withParams($params)
            ->sign($this->signature)
            ->serialize(ServiceRequest::FORMAT_QUERY);
    }

    private function getRequestHeaders(Image $image, array $request) {
        $headers = [
            'X-Phast-Image-API-Client' => $this->getRequestToken(),
            'Content-Type' => 'application/octet-stream',
        ];
        if (isset($request['preferredType']) && $request['preferredType'] == Image::TYPE_WEBP) {
            $headers['Accept'] = 'image/webp';
        }
        return $headers;
    }

    private function getRequestToken() {
        $token_parts = [];
        foreach (['host-name', 'request-uri', 'plugin-version'] as $key) {
            $token_parts[$key] = $this->config[$key];
        }
        $token_parts['php'] = PHP_VERSION;
        return json_encode($token_parts);
    }
}
