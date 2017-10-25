<?php

namespace Kibo\Phast\Filters\Image;

use Kibo\Phast\Filters\Image\ImageImplementations\DummyImage;
use PHPUnit\Framework\TestCase;

class ResizerImageFilterTest extends TestCase {

    public function testNoResizeWithNoHeight() {
        $this->checkResizing('75x50', '75x50', '100');
    }

    public function testNoResizeWithNoWidth() {
        $this->checkResizing('75x50', '75x50', 'x100');
    }

    public function testNotResizingWhenImageIsSmall() {
        $this->checkResizing('75x50', '75x50', '100x80');
    }

    public function testResizingByWidth() {
        $this->checkResizing('150x75', '100x50', '100x80');
    }

    public function testResizingByHeight() {
        $this->checkResizing('75x150', '40x80', '100x80');
    }

    public function testSelectingTheLargerVersionByHeight() {
        $this->checkResizing('500x300', '333x200', '100x200');
    }

    public function testSelectingTheLargerVersionByWidth() {
        $this->checkResizing('300x500', '200x333', '200x100');
    }

    public function testResizingByWidthWhenOnlyWidth() {
        $this->checkResizing('150x300', '100x200', '100');
    }

    public function testResizingByHeightWhenOnlyHeight() {
        $this->checkResizing('300x150', '200x100', 'x100');
    }


    private function checkResizing($imageSize, $expectedSize, $maxSize) {
        list ($imageWidth, $imageHeight) = explode('x', $imageSize);
        list ($expectedWidth, $expectedHeight) = explode('x', $expectedSize);
        list ($maxWidth, $maxHeight) = explode('x', $maxSize);
        $resizer = new ResizerImageFilter($maxWidth, $maxHeight);
        $image = new DummyImage($imageWidth, $imageHeight);
        $resizer->transformImage($image);
        $this->assertEquals($expectedWidth, $image->getWidth());
        $this->assertEquals($expectedHeight, $image->getHeight());
    }

}
