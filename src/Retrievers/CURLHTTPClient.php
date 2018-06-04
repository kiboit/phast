<?php

namespace Kibo\Phast\Retrievers;

use Kibo\Phast\Exceptions\RuntimeException;
use Kibo\Phast\ValueObjects\URL;

class CURLHTTPClient implements HTTPClient {

    public function __construct() {
        if (!function_exists('curl_init')) {
            throw new RuntimeException("cURL must be installed to use RemoteCURLBackend");
        }
    }

    public function get(URL $url, array $headers = []) {
        $ch = curl_init((string)$url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $this->makeHeaders($headers),
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5
        ]);
        $response = @curl_exec($ch);
        if ($response === false) {
            return false;
        }
        $info = curl_getinfo($ch);
        if (!preg_match('/^2/', $info['http_code'])) {
            return false;
        }
        return $response;
    }

    private function makeHeaders(array $headers) {
        $result = [];
        foreach ($headers as $k => $v) {
            $result[] = "$k: $v";
        }
        return $result;
    }

}
