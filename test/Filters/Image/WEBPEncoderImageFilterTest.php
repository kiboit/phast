<?php

namespace Kibo\Phast\Filters\Image;

use Kibo\Phast\Filters\Image\ImageImplementations\DummyImage;
use PHPUnit\Framework\TestCase;

class WEBPEncoderImageFilterTest extends TestCase {

    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $request;

    /**
     * @var WEBPEncoderImageFilter
     */
    private $filter;

    public function setUp() {
        parent::setUp();
        $this->config = ['enabled' => true, 'compression' => 80];
        $this->request = ['preferredType' => Image::TYPE_WEBP];
        $this->filter = $this->getFilter();
    }

    public function testEncoding() {
        $image = new DummyImage();
        $image->setType(Image::TYPE_JPEG);
        $image = $image->compress(10);

        $this->config['enabled'] = false;
        $this->assertSame($image, $this->getFilter()->transformImage($image, $this->request));

        $this->request['preferredType'] = Image::TYPE_PNG;
        $this->assertSame($image, $this->getFilter()->transformImage($image, $this->request));

        $this->config['enabled'] = true;
        $this->assertSame($image, $this->getFilter()->transformImage($image, $this->request));

        unset ($this->request['preferredType']);
        $this->assertSame($image, $this->getFilter()->transformImage($image, $this->request));

        $this->request['preferredType'] = Image::TYPE_WEBP;
        /** @var DummyImage $actual */
        $actual = $this->getFilter()->transformImage($image, $this->request);
        $this->assertNotSame($image, $actual);
        $this->assertEquals(Image::TYPE_WEBP, $actual->getType());
        $this->assertEquals(80, $actual->getCompression());
    }

    public function testChoosingImage() {
        $image = new DummyImage();

        $image->setImageString('super-super-long');
        $image->setTransformationString('short');
        $encoded = $this->filter->transformImage($image, $this->request);

        $image->setImageString('short');
        $image->setTransformationString('super-super-long');
        $nonEncoded = $this->filter->transformImage($image, $this->request);

        $this->assertNotSame($image, $encoded);
        $this->assertEquals('short', $encoded->getAsString());

        $this->assertSame($image, $nonEncoded);
    }

    public function testNotEncodingPNG() {
        $image = new DummyImage();
        $image->setType(Image::TYPE_PNG);
        $this->assertSame($image, $this->filter->transformImage($image, $this->request));
    }

    private function getFilter() {
        return new WEBPEncoderImageFilter($this->config);
    }

}
