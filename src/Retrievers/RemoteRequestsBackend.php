<?php


namespace Kibo\Phast\Retrievers;


use Kibo\Phast\ValueObjects\URL;

class RemoteRequestsBackend {

    public function retrieve(URL $url, $userAgent) {
        $headers = ['User-Agent' => $userAgent];
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
