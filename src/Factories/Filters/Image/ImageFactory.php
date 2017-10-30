<?php

namespace Kibo\Phast\Factories\Filters\Image;

use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\Filters\Image\ImageImplementations\GDImage;
use Kibo\Phast\Retrievers\LocalRetriever;
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
        $locator = new LocalRetriever($this->config['retrieverMap']);
        return new GDImage($url, $locator);
    }

}
