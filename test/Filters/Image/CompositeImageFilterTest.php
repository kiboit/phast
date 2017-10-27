<?php

namespace Kibo\Phast\Filters\Image;

use Kibo\Phast\Exceptions\ImageException;
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
        $this->getMockFilter();
        $this->getMockFilter();
        $this->filter->apply($this->image);
    }

    public function testReturnOriginalWhenNoFilters() {
        $actual = $this->filter->apply($this->image);
        $this->assertSame($this->image, $actual);
    }

    public function testReturnNewImageWhenChangesToSmaller() {
        $this->image->setImageString('very-very-big');
        $this->image->setTransformationString('small');
        $this->getMockFilter();
        $actual = $this->filter->apply($this->image);
        $this->assertNotSame($this->image, $actual);
    }

    public function testReturnOriginalImageWhenChangesToBigger() {
        $this->image->setImageString('small');
        $this->image->setTransformationString('very-very-big');
        $this->getMockFilter();
        $actual = $this->filter->apply($this->image);
        $this->assertSame($this->image, $actual);
    }

    public function testReturnOriginalOnFilterException() {
        $filter = $this->createMock(ImageFilter::class);
        $filter->method('transformImage')
            ->willThrowException(new \Exception());
        $this->filter->addImageFilter($filter);
        $actual = $this->filter->apply($this->image);
        $this->assertSame($this->image, $actual);
    }

    public function testReturnOriginalOnImageException() {
        $image = $this->createMock(DummyImage::class);
        $image->expects($this->once())
            ->method('getAsString')
            ->willThrowException(new ImageException());
        $image->method('transform')
            ->willReturn($image);
        $actual = $this->filter->apply($image);
        $this->assertSame($image, $actual);
    }

    private function getMockFilter() {
        $mock = $this->createMock(ImageFilter::class);
        $mock->expects($this->once())
              ->method('transformImage')
              ->with($this->image)
              ->willReturn($this->image);
        $this->filter->addImageFilter($mock);
        return $mock;
    }
}
