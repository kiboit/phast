<?php

namespace Kibo\Phast\Retrievers;

use Kibo\Phast\ValueObjects\URL;

class RemoteRetriever implements Retriever {
    use DynamicCacheSaltTrait;

    public function retrieve(URL $url) {
        $ch = curl_init((string)$url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['User-Agent: Mozilla/5.0 (X11; Fedora; Linux x86_64; rv:56.0) Gecko/20100101 Firefox/56.0'],
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
