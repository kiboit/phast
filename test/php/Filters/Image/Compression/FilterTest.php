<?php

namespace Kibo\Phast\Filters\Image\Compression;

use Kibo\Phast\Filters\Image\Image;
use Kibo\Phast\Filters\Image\ImageImplementations\DummyImage;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase {

    public function testCompressionSetting() {
        $compressions = [
            Image::TYPE_JPEG => '80',
            Image::TYPE_PNG  => '9'
        ];
        $filter = new Filter($compressions);
        $image = new DummyImage();

        $image->setType(Image::TYPE_JPEG);
        $actual = $filter->transformImage($image, []);
        $this->assertEquals($compressions[Image::TYPE_JPEG], $actual->getCompression());

        $image->setType(Image::TYPE_PNG);
        $actual = $filter->transformImage($image, []);
        $this->assertEquals($compressions[Image::TYPE_PNG], $actual->getCompression());

        $image->setType('not-existing');
        $actual = $filter->transformImage($image, []);
        $this->assertSame($image, $actual);
    }

    public function testGeneratingCacheSalt() {
        $filter1 = new Filter(['type1' => 20, 'type2' => 30]);
        $filter2 = new Filter(['type1' => 20, 'type2' => 31]);
        $this->assertNotEquals($filter1->getCacheSalt([]), $filter2->getCacheSalt([]));
    }
}
