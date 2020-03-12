<?php

namespace Kibo\Phast\Retrievers;

use Kibo\Phast\HTTP\ClientFactory;

class RemoteRetrieverFactory {
    public function make(array $config) {
        return new RemoteRetriever(
            (new ClientFactory())->make($config)
        );
    }
}
