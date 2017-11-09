<?php

namespace Kibo\Phast\Retrievers;

use Kibo\Phast\ValueObjects\URL;

class RemoteRetriever implements Retriever {

    public function retrieve(URL $url) {
        return @file_get_contents((string)$url);
    }

    public function getLastModificationTime(URL $url) {
        throw new \RuntimeException('Not implemented');
    }

}
