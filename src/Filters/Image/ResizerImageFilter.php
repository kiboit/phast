<?php

namespace Kibo\Phast\Filters\Image;

class ResizerImageFilter implements ImageFilter {

    /**
     * @var integer
     */
    private $maxWidth;

    /**
     * @var integer
     */
    private $maxHeight;

    /**
     * ResizerImageFilter constructor.
     *
     * @param int $defaultMaxWidth Set to zero for no limit on width
     * @param int $defaultMaxHeight Set to zero for no limit on height
     */
    public function __construct($defaultMaxWidth, $defaultMaxHeight) {
        $this->maxWidth = (int)$defaultMaxWidth;
        $this->maxHeight = (int)$defaultMaxHeight;
    }

    /**
     * Resizes image, keeping its original proportions,
     * to a maximum width ot height, set at construction time or passed in the request.
     * If both width and height of the image exceed the maximums set,
     * the image will be resized to the bigger possible size.
     *
     * @param Image $image
     * @param array $request
     * @return Image
     */
    public function transformImage(Image $image, array $request) {
        if (isset ($request['width']) || isset ($request['height'])) {
            $maxWidth  = isset ($request['width'])  ? (int)$request['width']  : 0;
            $maxHeight = isset ($request['height']) ? (int)$request['height'] : 0;
        } else {
            $maxWidth = $this->maxWidth;
            $maxHeight = $this->maxHeight;
        }

        $hasBiggerWidth  = $maxWidth  && $image->getWidth()  > $maxWidth;
        $hasBiggerHeight = $maxHeight && $image->getHeight() > $maxHeight;

        if ($hasBiggerWidth && $hasBiggerHeight) {
            $sizeW = $this->getNewSizeByWidth($image, $maxWidth);
            $sizeH = $this->getNewSizeByHeight($image, $maxHeight);
            if ($this->isBiggerSize($sizeW, $sizeH)) {
                return $this->setSize($image, $sizeW);
            }
            return $this->setSize($image, $sizeH);
        } else if ($hasBiggerWidth) {
            return $this->setSize($image, $this->getNewSizeByWidth($image, $maxWidth));
        } else if ($hasBiggerHeight) {
            return $this->setSize($image, $this->getNewSizeByHeight($image, $maxHeight));
        }
        return $image;
    }

    /**
     * Calculate a new size for an image,
     * keeping its original proportions,
     * with a certain maximum width.
     *
     * @param Image $image
     * @param int $maxWidth
     * @return array
     */
    private function getNewSizeByWidth(Image $image, $maxWidth) {
        return [
            $maxWidth,
            $this->calculateNewSide(
                $image->getWidth(),
                $image->getHeight(),
                $maxWidth
            )
        ];
    }

    /**
     * Calculate a new size for an image,
     * keeping its original proportions,
     * with a certain maximum height.
     *
     * @param Image $image
     * @param int $maxHeight
     * @return array
     */
    private function getNewSizeByHeight(Image $image, $maxHeight) {
        return [
            $this->calculateNewSide(
                $image->getHeight(),
                $image->getWidth(),
                $maxHeight
            ),
            $maxHeight
        ];
    }

    /**
     * Calculates a side of an image to a new size
     * that would follow the images proportions,
     * based on the old and new sizes of the other side.
     *
     * @param integer $oldSideA The old size of the other side
     * @param integer $sideB The current size of the side being calculated
     * @param integer $newSideA The new size of the other side
     * @return integer
     */
    private function calculateNewSide($oldSideA, $sideB, $newSideA) {
        $proportion = $oldSideA / $sideB;
        return (int)round($newSideA / $proportion);
    }

    /**
     * Sets a size to an image
     *
     * @param Image $image
     * @param array $size
     * @return Image
     */
    private function setSize(Image $image, array $size) {
        return $image->resize($size[0], $size[1]);
    }

    /**
     * Tells an image size is bigger than another.
     * An image size is bigger than another when the
     * product of it width and height is bigger than
     * the one of the other image.
     *
     * @param array $sizeA
     * @param array $sizeB
     * @return bool - true is $sizeA is bigger that $sizeB, false otherwise
     */
    private function isBiggerSize(array $sizeA, array $sizeB) {
        return $sizeA[0] * $sizeA[1] > $sizeB[0] * $sizeB[1];
    }

}
