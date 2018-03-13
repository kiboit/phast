<?php

namespace Kibo\Phast\Filters\Image\WEBPEncoder;

use Kibo\Phast\Filters\Image\Image;
use Kibo\Phast\Filters\Image\ImageFilter;
use Kibo\Phast\Logging\LoggingTrait;

class Filter implements ImageFilter {
    use LoggingTrait;

    private $compression;

    public function __construct(array $config) {
        $this->compression = $config['compression'];
    }

    public function getCacheSalt(array $request) {
        $salt = 'webp-compression-' . $this->compression;
        if (isset ($request['preferredType'])) {
            $salt .= '-type-' . $request['preferredType'];
        }
        return $salt;
    }

    public function transformImage(Image $image, array $request) {
        $encode = isset ($request['preferredType'])
                  && $request['preferredType'] == Image::TYPE_WEBP
                  && $image->getType() != Image::TYPE_PNG;
        if (!$encode) {
            $this->logger()->info('Not recoding to WEBP');
            return $image;
        }
        $encoded = $image->encodeTo(Image::TYPE_WEBP)
                         ->compress($this->compression);
        if ($encoded->getSizeAsString() <= $image->getSizeAsString()) {
            $this->logger()->info('Recoded to WEBP');
            return $encoded;
        }
        $this->logger()->info('Recoded to WEBP but original was smaller, so returning it instead');
        return $image;
    }

}
