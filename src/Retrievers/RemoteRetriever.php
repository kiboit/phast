<?php

namespace Kibo\Phast\Retrievers;

use Kibo\Phast\Exceptions\RuntimeException;
use Kibo\Phast\ValueObjects\URL;

class RemoteRetriever implements Retriever {
    use DynamicCacheSaltTrait;

    private $client;

    public function __construct(HttpClient $client) {
        $this->client = $client;
    }

    public function retrieve(URL $url) {
        return $this->client->retrieve($url, [
            'User-Agent' => 'Mozilla/5.0 (X11; Fedora; Linux x86_64; rv:56.0) Gecko/20100101 Firefox/56.0'
        ]);
    }
}
