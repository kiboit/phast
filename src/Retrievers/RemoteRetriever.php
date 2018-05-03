<?php

namespace Kibo\Phast\Retrievers;

use Kibo\Phast\Exceptions\RuntimeException;
use Kibo\Phast\ValueObjects\URL;

class RemoteRetriever implements Retriever {
    use DynamicCacheSaltTrait;

    private $backend;

    public function __construct() {
        if (class_exists(\Requests::class)) {
            $this->backend = new RemoteRequestsBackend();
        } else if (function_exists('curl_init')) {
            $this->backend = new RemoteCURLBackend();
        } else {
            throw new RuntimeException('Could not find appropriate backend for remote retriever');
        }
    }

    public function retrieve(URL $url) {
        return $this->backend->retrieve($url, 'Mozilla/5.0 (X11; Fedora; Linux x86_64; rv:56.0) Gecko/20100101 Firefox/56.0');
    }
}
