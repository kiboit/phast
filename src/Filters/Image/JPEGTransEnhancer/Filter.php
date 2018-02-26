<?php

namespace Kibo\Phast\Filters\Image\JPEGTransEnhancer;

use Kibo\Phast\Filters\Image\ExternalAppImageFilter;
use Kibo\Phast\Filters\Image\Image;

class Filter extends ExternalAppImageFilter {

    protected function shouldApply(Image $image) {
        return $image->getType() == Image::TYPE_JPEG;
    }

    protected function getDefaultBinName() {
        return 'jpegtran';
    }

    protected function getCmdArgs() {
        return ' -copy none -optimize';
    }

}
