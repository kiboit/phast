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
        $mock1 = $this->getMockFilter();
        $mock2 = $this->getMockFilter();
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

    public function testReturnOriginalOnFilterException() {
        $filter = $this->createMock(ImageFilter::class);
        $filter->method('transformImage')
            ->willThrowException(new \Exception());
        $this->filter->addFilter($filter);
        $this->image->setOriginalString('original-string');
        $actual = $this->filter->apply($this->image);
        $this->assertEquals('original-string', $actual);
    }

    public function testReturnOriginalOnImageException() {
        $image = $this->createMock(Image::class);
        $image->expects($this->once())
            ->method('getAsString')
            ->willThrowException(new ImageException());
        $image->expects($this->once())
            ->method('getOriginalAsString')
            ->willReturn('original');
        $actual = $this->filter->apply($image);
        $this->assertEquals('original', $actual);
    }

    private function getMockFilter() {
        $mock = $this->createMock(ImageFilter::class);
        $mock->expects($this->once())
              ->method('transformImage')
              ->with($this->image);
        return $mock;
    }
}
