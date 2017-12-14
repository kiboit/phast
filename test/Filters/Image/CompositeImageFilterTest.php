<?php

namespace Kibo\Phast\Filters\Image;

use Kibo\Phast\Exceptions\ImageProcessingException;
use Kibo\Phast\Filters\Image\ImageImplementations\DummyImage;
use Kibo\Phast\Logging\Log;
use Kibo\Phast\Logging\LogEntry;
use Kibo\Phast\Logging\Logger;
use Kibo\Phast\Logging\LogLevel;
use Kibo\Phast\Logging\LogWriter;
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

    public function tearDown() {
        parent::tearDown();
        Log::initWithDummy();
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

    public function testContinueWhenException() {
        $this->getThrowingFilter();
        $actualImage = new DummyImage();
        $this->getMockFilter($actualImage);
        $actual = $this->filter->apply($this->image, []);
        $this->assertSame($this->image, $actual);
    }

    public function testLoggingWhenException() {
        $actualMessage = null;
        $actualContext = null;
        $writer = $this->createMock(LogWriter::class);
        $writer->method('writeEntry')
            ->willReturnCallback(function (LogEntry $entry) use (&$actualMessage, &$actualContext) {
                if ($entry->getLevel() == LogLevel::CRITICAL) {
                    $actualMessage = $entry->getMessage();
                    $actualContext = $entry->getContext();
                }
            });
        Log::setLogger(new Logger($writer));

        $this->getThrowingFilter();
        $this->filter->apply($this->image, []);

        $this->assertNotNull($actualContext);
        $expected = ['filter', 'exceptionClass', 'message', 'code', 'file', 'line'];
        foreach ($expected as $element) {
            $this->assertContains("{$element}", $actualMessage);
            $this->assertArrayHasKey($element, $actualContext);
        }
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

    private function getThrowingFilter() {
        $filter = $this->createMock(ImageFilter::class);
        $filter->expects($this->once())
            ->method('transformImage')
            ->willThrowException(new ImageProcessingException());
        $this->filter->addImageFilter($filter);
        return $filter;
    }
}
