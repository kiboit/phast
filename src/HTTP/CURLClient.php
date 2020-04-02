<?php

namespace Kibo\Phast\HTTP;

use Kibo\Phast\HTTP\Exceptions\HTTPError;
use Kibo\Phast\HTTP\Exceptions\NetworkError;
use Kibo\Phast\ValueObjects\URL;

class CURLClient implements Client {
    public function get(URL $url, array $headers = []) {
        $this->checkCURL();
        return $this->request($url, $headers);
    }

    public function post(URL $url, $data, array $headers = []) {
        $this->checkCURL();
        return $this->request($url, $headers, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
        ]);
    }

    private function checkCURL() {
        if (!function_exists('curl_init')) {
            throw new NetworkError('cURL is not installed');
        }
    }

    private function request(URL $url, array $headers = [], array $opts = []) {
        $response = new Response();
        $readHeader = function ($_, $headerLine) use ($response) {
            if (strpos($headerLine, 'HTTP/') === 0) {
                $response->setHeaders([]);
            } else {
                list($name, $value) = explode(':', $headerLine, 2);
                if (trim($name) !== '') {
                    $response->setHeader($name, trim($value));
                }
            }
            return strlen($headerLine);
        };
        $ch = curl_init((string) $url);
        curl_setopt_array($ch, $opts + [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $this->makeHeaders($headers),
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_HEADERFUNCTION => $readHeader,
            CURLOPT_CAINFO => __DIR__ . '/cacert.pem',
            CURLOPT_ENCODING => '',
        ]);
        $responseText = @curl_exec($ch);
        if ($responseText === false) {
            throw new NetworkError(curl_error($ch), curl_errno($ch));
        }
        $info = curl_getinfo($ch);
        if (!preg_match('/^2/', $info['http_code'])) {
            throw new HTTPError($info['http_code']);
        }
        $response->setCode($info['http_code']);
        $response->setContent($responseText);
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
