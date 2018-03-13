<?php

namespace Kibo\Phast\Filters\Image\Resizer;

use Kibo\Phast\Filters\Image\ImageImplementations\DummyImage;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase {

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

    public function testMaxSizesPriorityWidthOnly() {
        $this->checkResizing('75x150', '50x100', '100x300', '50');
    }

    public function testMaxSizesPriorityHeightOnly() {
        $this->checkResizing('75x150', '50x100', '100x300', 'x100');
    }

    public function testMaxSizesPriorityWidthAndHeight() {
        $this->checkResizing('75x150', '50x100', '100x300', '50x300');
    }

    private function checkResizing($imageSize, $expectedSize, $defaultMaxSize, $priorityMaxSize = null) {
        list ($imageWidth, $imageHeight) = explode('x', $imageSize);
        @list ($expectedWidth, $expectedHeight) = explode('x', $expectedSize);
        @list ($defaultMaxWidth, $defaultMaxHeight) = explode('x', $defaultMaxSize);
        @list ($priorityMaxWidth, $priorityMaxHeight) = explode('x', $priorityMaxSize);
        $request = [];
        if ($priorityMaxWidth) {
            $request['width'] = $priorityMaxWidth;
        }
        if ($priorityMaxHeight) {
            $request['height'] = $priorityMaxHeight;
        }
        $image = new DummyImage($imageWidth, $imageHeight);
        $resizer = new Filter($defaultMaxWidth, $defaultMaxHeight);
        $actual = $resizer->transformImage($image, $request);
        $this->assertEquals($expectedWidth, $actual->getWidth());
        $this->assertEquals($expectedHeight, $actual->getHeight());
    }

    /**
     * @dataProvider generatingCacheSaltData
     */
    public function testGeneratingCacheSalt($constructorSize, $requestSize = null) {
        static $lastSalt, $called = false;
        list ($defaultWidth, $defaultHeight) = explode('x', $constructorSize);
        $params = [];
        if ($requestSize) {
            @list ($requestWidth, $requestHeight) = explode('x', $requestSize);
            if ($requestWidth) {
                $params['width'] = $requestWidth;
            }
            if ($requestHeight) {
                $params['height'] = $requestHeight;
            }
        }
        $filter = new Filter($defaultWidth, $defaultHeight);
        $salt = $filter->getCacheSalt($params);
        if ($called) {
            $this->assertNotEquals($lastSalt, $salt);
        }
        $called = true;
        $lastSalt = $salt;
    }

    public function generatingCacheSaltData() {
        return [
            ['100x200'],
            ['100x300'],
            ['300x100'],
            ['300x100', '150x250'],
            ['300x100', '250x150'],
            ['300x100', '250'],
            ['300x100', '250x150'],
            ['300x100', 'x150'],
        ];
    }

}
