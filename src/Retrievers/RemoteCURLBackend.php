<?php


namespace Kibo\Phast\Retrievers;


use Kibo\Phast\ValueObjects\URL;

class RemoteCURLBackend {

    public function retrieve(URL $url, $userAgent) {
        $ch = curl_init((string)$url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['User-Agent: ' . $userAgent],
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

}
