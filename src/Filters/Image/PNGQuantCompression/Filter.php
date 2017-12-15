<?php

namespace Kibo\Phast\Filters\Image\PNGQuantCompression;

use Kibo\Phast\Filters\Image\ExternalAppImageFilter;
use Kibo\Phast\Filters\Image\Image;

class Filter extends ExternalAppImageFilter {

    protected function shouldApply(Image $image) {
        return $image->getType() == Image::TYPE_PNG;
    }

    protected function getCommand() {
        $cmd = $this->config['cmdpath'];
        if (isset ($this->config['quality'])) {
            $cmd .= ' --quality=' . $this->config['quality'];
        }
        if (isset ($this->config['speed'])) {
            $cmd .= ' --speed=' . $this->config['speed'];
        }
        $cmd .= ' - ';
        return $cmd;
    }

}
