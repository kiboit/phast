<?php

namespace Kibo\Phast\Filters\Image\ImageImplementations;

use Kibo\Phast\Exceptions\ImageProcessingException;
use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\Filters\Image\Image;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\ValueObjects\URL;
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
        $image = $this->makeImage($string);

        $this->assertEquals(150, $image->getWidth());
        $this->assertEquals(200, $image->getHeight());
        $this->assertEquals($type, $image->getType());

        $image = $image->resize(75, 100);
        $this->assertEquals(75, $image->getWidth());
        $this->assertEquals(100, $image->getHeight());

        $resized = $image->getAsString();
        $info = getimagesizefromstring($resized);
        $this->assertEquals(75, $info[0]);
        $this->assertEquals(100, $info[1]);
        $this->assertEquals($gdtype, $info[2]);
    }

    public function testCompressingPNG() {
        $this->checkCompressing('imagepng', 1, 8);
    }

    public function testCompressingJPEG() {
        $this->checkCompressing('imagejpeg', 80, 20);
    }

    public function testRecoding() {
        $jpeg = $this->getImageString('imagejpeg', 80, 'Hello, World');
        $image = $this->makeImage($jpeg);
        $recodedPng = $image->encodeTo(Image::TYPE_PNG);
        $recodedWebp = $image->encodeTo(Image::TYPE_WEBP);

        $this->assertEquals(Image::TYPE_JPEG, $image->getType());
        $this->assertEquals(Image::TYPE_PNG, $recodedPng->getType());
        $this->assertEquals(Image::TYPE_WEBP, $recodedWebp->getType());

        $actualPng = getimagesizefromstring($recodedPng->getAsString())[2];
        $actualJpg = getimagesizefromstring($image->getAsString())[2];

        $this->assertEquals(IMAGETYPE_PNG, $actualPng);
        $this->assertEquals(IMAGETYPE_JPEG, $actualJpg);

        // getimagesizefromstring returns false for webp
        $this->assertStringStartsWith('RIFF', $recodedWebp->getAsString());
    }

    public function testExceptionOnBadImageAsString() {
        $image = $this->makeImage('asdasd');
        $this->expectException(ImageProcessingException::class);
        $image->compress(9)->getAsString();
    }

    public function testExceptionOnBadImageInfo() {
        $image = $this->makeImage('asdasd');
        $this->expectException(ImageProcessingException::class);
        $image->getWidth();
    }

    public function testExceptionOnUnretrievableImage() {
        $image = $this->makeImage(false);
        $this->expectException(ItemNotFoundException::class);
        $image->getAsString();
    }

    public function testOriginalSizeAndImage() {
        $string = $this->getImageString('imagepng', 9, 'Hello, World!');
        $image = $this->makeImage($string);
        $this->assertEquals(strlen($string), $image->getSizeAsString());
        $this->assertSame($string, $image->getAsString());
    }

    private function checkCompressing($imagecb, $inputCompression, $outputCompression) {
        $string = $this->getImageString($imagecb, $inputCompression, 'Hello, World!');
        $image = $this->makeImage($string);

        $actual = $image->compress($outputCompression)->getAsString();
        $this->assertNotEmpty($actual);
        $this->assertLessThan(strlen($string), strlen($actual));
    }

    /**
     * @param $imageString
     * @return GDImage
     */
    private function makeImage($imageString) {
        $url = URL::fromString('http://kibo.test/the-image');
        $retriever = $this->createMock(Retriever::class);
        $retriever->method('retrieve')
                  ->with($url)
                  ->willReturn($imageString);
        return new GDImage($url, $retriever);
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
