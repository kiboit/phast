<?php

namespace Kibo\Phast\Filters\Image;

use Kibo\Phast\Exceptions\LogicException;
use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\Retrievers\PostDataRetriever;
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
        if ($this->config['images']['api-mode']) {
            $retriever = new PostDataRetriever();
        } else {
            $retriever = new UniversalRetriever();
            $retriever->addRetriever(new LocalRetriever($this->config['retrieverMap']));
            $retriever->addRetriever((new RemoteRetrieverFactory())->make($this->config));
        }
        if (empty($this->config['images']['imageImplementation'])) {
            throw new LogicException("An image implementation must be configured");
        }
        $imageClass = $this->config['images']['imageImplementation'];
        if (!class_exists($imageClass)) {
            throw new LogicException("Image implementation does not exist: $imageClass");
        }
        return new $imageClass($url, $retriever);
    }

    /**
     * @param Resource $resource
     * @return Image
     */
    public function getForResource(Resource $resource) {
        return $this->getForURL($resource->getUrl());
    }

}
