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
     * @param int $priorityMaxWidth If set will override $defaultMaxWidth
     *                              and will set $defaultMaxHeight to 0
     *                              unless $priorityMaxHeight is specified
     * @param int $priorityMaxHeight If set will override $defaultMaxHeight
     *                              and will set $defaultMaxWidth to 0
     *                              unless $priorityMaxWidth is specified
     */
    public function __construct($defaultMaxWidth, $defaultMaxHeight, $priorityMaxWidth, $priorityMaxHeight) {
        $usePriority = (bool)$priorityMaxWidth || (bool)$priorityMaxHeight;
        if ($usePriority) {
            $this->maxWidth = (int)$priorityMaxWidth;
            $this->maxHeight = (int)$priorityMaxHeight;
        } else {
            $this->maxWidth = (int)$defaultMaxWidth;
            $this->maxHeight = (int)$defaultMaxHeight;
        }
    }

    /**
     * Resizes image, keeping its original proportions,
     * to a maximum width ot height, set at construction time.
     * If both width and height of the image exceed the maximums set,
     * the image will be resized to the bigger possible size.
     *
     * @param Image $image
     * @return Image
     */
    public function transformImage(Image $image, array $request) {
        $hasBiggerWidth  = $this->maxWidth  && $image->getWidth()  > $this->maxWidth;
        $hasBiggerHeight = $this->maxHeight && $image->getHeight() > $this->maxHeight;

        if ($hasBiggerWidth && $hasBiggerHeight) {
            $sizeW = $this->getNewSizeByWidth($image);
            $sizeH = $this->getNewSizeByHeight($image);
            if ($this->isBiggerSize($sizeW, $sizeH)) {
                return $this->setSize($image, $sizeW);
            }
            return $this->setSize($image, $sizeH);
        } else if ($hasBiggerWidth) {
            return $this->setSize($image, $this->getNewSizeByWidth($image));
        } else if ($hasBiggerHeight) {
            return $this->setSize($image, $this->getNewSizeByHeight($image));
        }
        return $image;
    }

    /**
     * Calculate a new size for an image,
     * keeping its original proportions,
     * with a certain maximum width.
     *
     * @param Image $image
     * @return array
     */
    private function getNewSizeByWidth(Image $image) {
        return [
            $this->maxWidth,
            $this->calculateNewSide(
                $image->getWidth(),
                $image->getHeight(),
                $this->maxWidth
            )
        ];
    }

    /**
     * Calculate a new size for an image,
     * keeping its original proportions,
     * with a certain maximum height.
     *
     * @param Image $image
     * @return array
     */
    private function getNewSizeByHeight(Image $image) {
        return [
            $this->calculateNewSide(
                $image->getHeight(),
                $image->getWidth(),
                $this->maxHeight
            ),
            $this->maxHeight
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
