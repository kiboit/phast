<?php

namespace Kibo\Phast\Services\Images;

use Kibo\Phast\Filters\Image\Composite\Factory as CompositeImageFilterFactory;
use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\Retrievers\PostDataRetriever;
use Kibo\Phast\Retrievers\RemoteRetrieverFactory;
use Kibo\Phast\Retrievers\UniversalRetriever;
use Kibo\Phast\Security\ServiceSignatureFactory;

class Factory {
    public function make(array $config) {
        if ($config['images']['api-mode']) {
            $retriever = new PostDataRetriever();
        } else {
            $retriever = new UniversalRetriever();
            $retriever->addRetriever(new LocalRetriever($config['retrieverMap']));
            $retriever->addRetriever((new RemoteRetrieverFactory())->make($config));
        }
        return new Service(
            (new ServiceSignatureFactory())->make($config),
            $config['images']['whitelist'],
            $retriever,
            (new CompositeImageFilterFactory($config))->make(),
            $config
        );
    }
}
