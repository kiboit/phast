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
        $this->getMockFilter();
        $this->getMockFilter();
        $this->filter->apply($this->image, []);
    }

    public function testReturnOriginalWhenNoFilters() {
        $actual = $this->filter->apply($this->image, []);
        $this->assertSame($this->image, $actual);
    }

    public function testReturnNewImageWhenChangesToSmaller() {
        $this->image->setImageString('very-very-big');
        $small = new DummyImage();
        $small->setImageString('small');
        $this->getMockFilter($small);
        $actual = $this->filter->apply($this->image, []);
        $this->assertSame($small, $actual);
    }

    public function testReturnOriginalImageWhenChangesToBigger() {
        $this->image->setImageString('small');
        $big = new DummyImage();
        $big->setImageString('very-very-big');
        $this->getMockFilter($big);
        $actual = $this->filter->apply($this->image, []);
        $this->assertSame($this->image, $actual);
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
