<?php

namespace Kibo\Phast\Filters\Image;

interface ImageFilter {

    /**
     * @param Image $image
     * @param array $request
     * @return Image
     */
    public function transformImage(Image $image, array $request);

}
