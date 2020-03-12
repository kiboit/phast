<?php

namespace Kibo\Phast\Filters\Image;

interface Image {
    const TYPE_JPEG  = 'image/jpeg';

    const TYPE_PNG   = 'image/png';

    const TYPE_WEBP  = 'image/webp';

    /**
     * @return integer
     */
    public function getWidth();

    /**
     * @return integer
     */
    public function getHeight();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getAsString();

    /**
     * @return integer
     */
    public function getSizeAsString();

    /**
     * @param integer $width
     * @param integer $height
     * @return Image
     */
    public function resize($width, $height);

    /**
     * @param integer $compression
     * @return Image
     */
    public function compress($compression);

    /**
     * @param string $type - One of Image::TYPE_JPEG, Image::TYPE_PNG or Image::TYPE_WEBP
     * @return Image
     */
    public function encodeTo($type);
}
