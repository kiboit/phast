<?php

namespace Kibo\Phast\Filters\Image;

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
        $filter->transformImage($image);
        $this->assertEquals($compressions[Image::TYPE_JPEG], $image->getCompression());

        $image->setType(Image::TYPE_PNG);
        $filter->transformImage($image);
        $this->assertEquals($compressions[Image::TYPE_PNG], $image->getCompression());

        $image->setCompression('override-old');
        $image->setType('not-existing');
        $filter->transformImage($image);
        $this->assertEquals('override-old', $image->getCompression());

    }

}
