<?php

namespace Kibo\Phast\Filters\Image\ImageImplementations;

use Kibo\Phast\Filters\Image\Image;
use Kibo\Phast\Filters\Image\ImageException;
use PHPUnit\Framework\TestCase;

class GDImageTest extends TestCase {

    public function testImageSizeAndTypeForJPEG() {
        $this->checkImageSizeAndType(Image::TYPE_JPEG, IMG_JPEG, 'imagejpeg');
    }

    public function testImageSizeAndTypeForPNG() {
        $this->checkImageSizeAndType(Image::TYPE_PNG, 3, 'imagepng');
    }

    private function checkImageSizeAndType($type, $gdtype, $imgcallback) {
        $string = $this->getImageString($imgcallback);
        $image = new GDImage($string);

        $this->assertEquals(150, $image->getWidth());
        $this->assertEquals(200, $image->getHeight());
        $this->assertEquals($type, $image->getType());

        $image->setWidth(75);
        $image->setHeight(100);
        $this->assertEquals(75, $image->getWidth());
        $this->assertEquals(100, $image->getHeight());

        $resized = $image->getAsString();
        $info = getimagesizefromstring($resized);
        $this->assertEquals(75, $info[0]);
        $this->assertEquals(100, $info[1]);
        $this->assertEquals($gdtype, $info[2]);
    }

    public function testCompressingPNG() {
        $this->checkCompressing('imagepng', 1, 8, false);
    }

    public function testCompressingJPEG() {
        $this->checkCompressing('imagejpeg', 80, 20);
    }

    public function testExceptionOnBadImageAsString() {
        $image = new GDImage('asdasd');
        $this->expectException(ImageException::class);
        $image->getAsString();
    }

    public function testExceptionOnBadImageInfo() {
        $image = new GDImage('asdasd');
        $this->expectException(ImageException::class);
        $image->getWidth();
    }

    public function testOriginalSizeAndImage() {
        $string = $this->getImageString('imagepng', 9, 'Hello, World!');
        $image = new GDImage($string);
        $this->assertEquals(strlen($string), $image->getOriginalFileSize());
        $this->assertSame($string, $image->getOriginalAsString());
    }


    private function checkCompressing($imagecb, $inputCompression, $outputCompression, $checkDefault = true) {
        $string = $this->getImageString($imagecb, $inputCompression, 'Hello, World!');
        $image = new GDImage($string);

        if ($checkDefault) {
            $actual1 = $image->getAsString();
            $this->assertNotEmpty($actual1);
            $this->assertGreaterThan(strlen($string), strlen($actual1));
        }

        $image->setCompression($outputCompression);
        $actual = $image->getAsString();
        $this->assertNotEmpty($actual);
        $this->assertLessThan(strlen($string), strlen($actual));
    }

    private function getImageString($callback, $compression = 0, $text = null) {
        $image = imagecreate(150, 200);
        $orange = imagecolorallocate($image, 220, 210, 60);
        if ($text) {
            imagestring($image, 3, 10, 9, $text, $orange);
        }
        ob_start();
        $callback($image, null, $compression);
        return ob_get_clean();
    }

}
