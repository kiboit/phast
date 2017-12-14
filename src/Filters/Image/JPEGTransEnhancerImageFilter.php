<?php

namespace Kibo\Phast\Filters\Image;

class JPEGTransEnhancerImageFilter extends ExternalAppImageFilter {

    protected function shouldApply(Image $image) {
        return $image->getType() == Image::TYPE_JPEG;
    }

    protected function getCommand() {
        return $this->config['cmdpath'] . ' -copy none -optimize';
    }

}
