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
        $small = new DummyImage();
        $small->setImageString('small');
        $this->getMockFilter($small);
        $actual = $this->filter->apply($this->image);
        $this->assertSame($small, $actual);
    }

    public function testReturnOriginalImageWhenChangesToBigger() {
        $this->image->setImageString('small');
        $big = new DummyImage();
        $big->setImageString('very-very-big');
        $this->getMockFilter($big);
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
        $actual = $this->filter->apply($image);
        $this->assertSame($image, $actual);
    }

    private function getMockFilter(Image $image = null) {
        $returnImage = is_null($image) ? $this->image : $image;
        $mock = $this->createMock(ImageFilter::class);
        $mock->expects($this->once())
              ->method('transformImage')
              ->with($this->image)
              ->willReturn($returnImage);
        $this->filter->addImageFilter($mock);
        return $mock;
    }
}
