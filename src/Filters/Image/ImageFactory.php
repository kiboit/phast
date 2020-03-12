<?php

namespace Kibo\Phast\Filters\Image;

use Kibo\Phast\Filters\Image\ImageImplementations\DefaultImage;
use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\Retrievers\RemoteRetrieverFactory;
use Kibo\Phast\Retrievers\UniversalRetriever;
use Kibo\Phast\ValueObjects\Resource;
use Kibo\Phast\ValueObjects\URL;

class ImageFactory {
    private $config;

    public function __construct(array $config) {
        $this->config = $config;
    }

    /**
     * @param URL $url
     * @return Image
     */
    public function getForURL(URL $url) {
        $retriever = new UniversalRetriever();
        $retriever->addRetriever(new LocalRetriever($this->config['retrieverMap']));
        $retriever->addRetriever((new RemoteRetrieverFactory())->make($this->config));
        return new DefaultImage($url, $retriever);
    }

    /**
     * @param Resource $resource
     * @return Image
     */
    public function getForResource(Resource $resource) {
        return $this->getForURL($resource->getUrl());
    }
}
