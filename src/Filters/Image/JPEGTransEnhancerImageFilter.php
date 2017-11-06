<?php

namespace Kibo\Phast\Filters\Image;

class JPEGTransEnhancerImageFilter extends ExternalAppImageFilter {

    protected function shouldApply(Image $image) {
        $this->config['enabled'] && $image->getType() == Image::TYPE_JPEG;
    }

    protected function getCommand() {
        return $this->config['cmdpath'] . ' -copy none -optimize';
    }

}
