<?php

namespace Kibo\Phast\Filters\Image;

use Kibo\Phast\Filters\Image\ImageImplementations\DummyImage;
use PHPUnit\Framework\TestCase;

class CompositeImageFilterTest extends TestCase {

    /**
     * @var DummyImage
     */
    private $image;

    /**
     * @var CompositeImageFilter
     */
    private $filter;

    public function setUp() {
        parent::setUp();
        $this->filter = new CompositeImageFilter();
        $this->image = new DummyImage();
    }

    public function testApplicationOnAllFilters() {
        $mock1 = $this->createMock(ImageFilter::class);
        $mock1->expects($this->once())
            ->method('transformImage')
            ->with($this->image);
        $mock2 = $this->createMock(ImageFilter::class);
        $mock2->expects($this->once())
            ->method('transformImage')
            ->with($this->image);

        $this->filter->addFilter($mock1);
        $this->filter->addFilter($mock2);
        $this->filter->apply($this->image);
    }

    public function testReturnsImageString() {
        $this->image->setOriginalString('a-longer-test-string');
        $this->image->setImageString('test-string');
        $actual = $this->filter->apply($this->image);
        $this->assertEquals('test-string', $actual);
    }

    public function testReturnsOriginalImageString() {
        $this->image->setOriginalString('12345');
        $this->image->setImageString('1234567890');
        $actual = $this->filter->apply($this->image);
        $this->assertEquals('12345', $actual);
    }


}
