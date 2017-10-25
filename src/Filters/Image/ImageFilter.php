<?php

namespace Kibo\Phast\Filters\Image;

interface ImageFilter {

    /**
     * @param Image $image
     * @return null
     */
    public function transformImage(Image $image);

}
