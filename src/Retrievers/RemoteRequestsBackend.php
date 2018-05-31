<?php


namespace Kibo\Phast\Retrievers;


use Kibo\Phast\ValueObjects\URL;

class RemoteRequestsBackend implements HttpClient {

    public function retrieve(URL $url, array $headers = []) {
        try {
            $response = \Requests::get((string)$url, $headers);
        } catch (\Exception $e) {
            return false;
        }
        if ($response->success) {
            return $response->body;
        }
        return false;
    }

}
