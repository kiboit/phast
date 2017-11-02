<?php

namespace Kibo\Phast\Filters\Image;

class WEBPEncoderImageFilter implements ImageFilter {

    private $encode;

    private $compression;

    public function __construct(array $config, array $request) {
        $this->encode = $config['enabled']
                        && isset ($request['preferredType'])
                        && $request['preferredType'] == Image::TYPE_WEBP;
        $this->compression = $config['compression'];
    }

    public function transformImage(Image $image) {
        if ($this->encode) {
            $encoded = $image->encodeTo(Image::TYPE_WEBP)
                ->compress($this->compression);
            if ($encoded->getSizeAsString() <= $image->getSizeAsString()) {
                return $encoded;
            }
        }
        return $image;
    }

}
