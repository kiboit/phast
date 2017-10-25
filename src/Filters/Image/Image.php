<?php

namespace Kibo\Phast\Filters\Image;

interface Image {

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
     * @return void
     */
    public function setWidth($width);

    /**
     * @param integer $height
     * @return void
     */
    public function setHeight($height);

}
