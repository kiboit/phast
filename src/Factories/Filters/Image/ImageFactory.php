<?php

namespace Kibo\Phast\Factories\Filters\Image;

use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\Filters\Image\ImageImplementations\GDImage;
use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\Retrievers\RemoteRetriever;
use Kibo\Phast\Retrievers\UniversalRetriever;
use Kibo\Phast\ValueObjects\URL;

class ImageFactory {

    private $config;

    public function __construct(array $config) {
        $this->config = $config;
    }

    /**
     * @param URL $url
     * @return GDImage
     * @throws ItemNotFoundException
     */
    public function getForURL(URL $url) {
        $retriever = new UniversalRetriever();
        $retriever->addRetriever(new LocalRetriever($this->config['retrieverMap']));
        $retriever->addRetriever(new RemoteRetriever());
        return new GDImage($url, $retriever);
    }

}
