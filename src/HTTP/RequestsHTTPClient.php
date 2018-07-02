<?php


namespace Kibo\Phast\HTTP;

use Kibo\Phast\ValueObjects\URL;

class RequestsHTTPClient implements Client {

    public function get(URL $url, array $headers = []) {
        $response = \Requests::get((string)$url, $headers);
        $response->throw_for_status();
        return $this->makePhastResponse($response);
    }

    public function post(URL $url, $data, array $headers = []) {
        $options = ['connect_timeout' => 2, 'timeout' => 10];
        $response = \Requests::post((string)$url, $headers, $data, $options);
        $response->throw_for_status();
        return $this->makePhastResponse($response);
    }

    private function makePhastResponse(\Requests_Response $requestsResponse) {
        $phastResponse = new Response();
        $phastResponse->setContent($requestsResponse->body);
        foreach ($requestsResponse->headers as $name => $value) {
            $phastResponse->setHeader($name, $value);
        }
        return $phastResponse;
    }


}
