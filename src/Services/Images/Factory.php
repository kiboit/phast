<?php

namespace Kibo\Phast\Services\Images;

use Kibo\Phast\Filters\Image\Composite\Factory as CompositeImageFilterFactory;
use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\Retrievers\RemoteRetriever;
use Kibo\Phast\Retrievers\UniversalRetriever;
use Kibo\Phast\Security\ServiceSignatureFactory;

class Factory {

    public function make(array $config) {
        $retriever = new UniversalRetriever();
        $retriever->addRetriever(new LocalRetriever($config['retrieverMap']));
        $retriever->addRetriever(new RemoteRetriever());
        return new Service(
            (new ServiceSignatureFactory())->make($config),
            $config['images']['whitelist'],
            $retriever,
            (new CompositeImageFilterFactory($config))->make()
        );
    }

}
