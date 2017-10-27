<?php

namespace Kibo\Phast\Filters\Image;

interface Image {

    const TYPE_JPEG  = 'image/jpeg';

    const TYPE_PNG   = 'image/png';

    /**
     * @return integer
     */
    public function getWidth();

    /**
     * @return integer
     */
    public function getHeight();

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
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getAsString();

    /**
     * @return Image
     */
    public function transform();

}
