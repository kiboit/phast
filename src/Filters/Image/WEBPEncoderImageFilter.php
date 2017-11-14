<?php

namespace Kibo\Phast\Filters\Image;

class WEBPEncoderImageFilter implements ImageFilter {

    private $enabled;

    private $compression;

    public function __construct(array $config) {
        $this->enabled = $config['enabled'];
        $this->compression = $config['compression'];
    }

    public function transformImage(Image $image, array $request) {
        $encode = $this->enabled
                  && isset ($request['preferredType'])
                  && $request['preferredType'] == Image::TYPE_WEBP
                  && $image->getType() != Image::TYPE_PNG;
        if (!$encode) {
            return $image;
        }
        $encoded = $image->encodeTo(Image::TYPE_WEBP)
                         ->compress($this->compression);
        if ($encoded->getSizeAsString() <= $image->getSizeAsString()) {
            return $encoded;
        }
        return $image;
    }

}
