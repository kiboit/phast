<?php

namespace Kibo\Phast\Filters\Image;

use Kibo\Phast\Filters\Image\ImageImplementations\DummyImage;
use PHPUnit\Framework\TestCase;

class CompressionImageFilterTest extends TestCase {

    public function testCompressionSetting() {
        $compressions = [
            Image::TYPE_JPEG => '80',
            Image::TYPE_PNG  => '9'
        ];
        $filter = new CompressionImageFilter($compressions);
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

}
