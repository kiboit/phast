<?php

namespace Kibo\Phast;

use Kibo\Phast\Factories\Filters\Image\CompositeImageFilterFactory;
use Kibo\Phast\Filters\Image\Image;
use Kibo\Phast\Filters\Image\ImageImplementations\GDImage;
use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\ValueObjects\URL;

class ImageFilteringService {

    public static function serve(array $config, array $request) {
        if (!isset ($request['src'])) {
            http_response_code(404);
            return;
        }
        $retriever = new LocalRetriever($config['retrieverMap']);
        $imageString = $retriever->retrieve(URL::fromString($request['src']));
        if (!$imageString) {
            http_response_code(404);
            return;
        }
        $image = new GDImage($imageString);
        $filterFactory = new CompositeImageFilterFactory();
        $filter = $filterFactory->make($config, $request);
        $mime = $image->getType() == Image::TYPE_JPEG ? 'image/jpeg' : 'image/png';
        header('Content-type: ' . $mime);
        echo $filter->apply($image);
    }

}
