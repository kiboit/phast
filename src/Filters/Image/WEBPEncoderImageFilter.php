<?php

namespace Kibo\Phast\Filters\Image;

use Kibo\Phast\Logging\LoggingTrait;

class WEBPEncoderImageFilter implements ImageFilter {
    use LoggingTrait;

    private $compression;

    public function __construct(array $config) {
        $this->compression = $config['compression'];
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
