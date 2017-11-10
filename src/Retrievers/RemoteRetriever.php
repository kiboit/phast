<?php

namespace Kibo\Phast\Retrievers;

use Kibo\Phast\ValueObjects\URL;

class RemoteRetriever implements Retriever {

    public function retrieve(URL $url) {
        $ch = curl_init((string)$url);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            ['User-Agent: Mozilla/5.0 (X11; Fedora; Linux x86_64; rv:56.0) Gecko/20100101 Firefox/56.0']
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        return @curl_exec($ch);
    }

    public function getLastModificationTime(URL $url) {
        throw new \RuntimeException('Not implemented');
    }

}
