<?php

namespace Kibo\Phast\Filters\Image;

interface ImageFilter {

    /**
     * @param Image $image
     * @return Image
     */
    public function transformImage(Image $image);

}
