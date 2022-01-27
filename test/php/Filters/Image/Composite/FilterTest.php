<?php

namespace Kibo\Phast\Filters\Image\Composite;

use Kibo\Phast\Filters\HTML\ImagesOptimizationService\ImageInliningManager;
use Kibo\Phast\Filters\Image\Exceptions\ImageProcessingException;
use Kibo\Phast\Filters\Image\Image;
use Kibo\Phast\Filters\Image\ImageFactory;
use Kibo\Phast\Filters\Image\ImageFilter;
use Kibo\Phast\Filters\Image\ImageImplementations\DummyImage;
use Kibo\Phast\Logging\Log;
use Kibo\Phast\Logging\LogEntry;
use Kibo\Phast\Logging\Logger;
use Kibo\Phast\Logging\LogLevel;
use Kibo\Phast\Logging\LogWriter;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\ValueObjects\Resource;
use Kibo\Phast\ValueObjects\URL;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase {
    /**
     * @var Resource
     */
    private $resource;

    /**
     * @var DummyImage
     */
    private $image;

    /**
     * @var Filter
     */
    private $filter;

    public function setUp(): void {
        parent::setUp();
        $this->image = new DummyImage();
        $this->image->setImageString('The test image');

        $this->resource = Resource::makeWithContent(URL::fromString('http://phast.test'), 'the-content');
        $factory = $this->createMock(ImageFactory::class);
        $factory->method('getForResource')
            ->with($this->resource)
            ->willReturn($this->image);
        $this->filter = new Filter($factory, $this->createMock(ImageInliningManager::class));
    }

    public function tearDown(): void {
        parent::tearDown();
        Log::initWithDummy();
    }

    public function testApplicationOnAllFilters() {
        $this->getMockFilter();
        $this->getMockFilter();
        $this->filter->apply($this->resource, []);
    }

    public function testReturnOriginalWhenNoFilters() {
        $actual = $this->filter->apply($this->resource, []);
        $this->assertSame($this->image->getAsString(), $actual->getContent());
    }

    public function testContinueWhenException() {
        $this->getThrowingFilter();
        $actualImage = new DummyImage();
        $actualImage->setImageString("we won't see this");
        $this->getMockFilter($actualImage);
        $actual = $this->filter->apply($this->resource, []);
        $this->assertSame($this->image->getAsString(), $actual->getContent());
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
        $this->filter->apply($this->resource, []);

        $this->assertNotNull($actualContext);
        $expected = ['filter', 'exceptionClass', 'message', 'code', 'file', 'line'];
        foreach ($expected as $element) {
            $this->assertStringContainsString("{$element}", $actualMessage);
            $this->assertArrayHasKey($element, $actualContext);
        }
    }

    public function testReturnNewImageWhenChangesToSmaller() {
        $this->image->setImageString('very-very-big');
        $small = new DummyImage();
        $small->setImageString('small');
        $this->getMockFilter($small);
        $actual = $this->filter->apply($this->resource, []);
        $this->assertSame($small->getAsString(), $actual->getContent());
    }

    public function testReturnOriginalImageWhenChangesToBigger() {
        $this->image->setImageString('small');
        $big = new DummyImage();
        $big->setImageString('very-very-big');
        $this->getMockFilter($big);
        $actual = $this->filter->apply($this->resource, []);
        $this->assertSame($this->image->getAsString(), $actual->getContent());
    }

    /**
     * @dataProvider correctHashData
     */
    public function testCorrectHash($location, $modTime, $filtersCount = 2, $filterSalt = null) {
        static $previousResults = [];
        $url = URL::fromString($location);
        $retriever = $this->createMock(Retriever::class);
        $retriever->expects($this->once())
            ->method('getCacheSalt')
            ->with($url)
            ->willReturn($modTime);
        $resource = Resource::makeWithRetriever($url, $retriever, 'mime');

        $filters = [];
        for ($i = 0; $i < $filtersCount; $i++) {
            $filter = $this->createMock(ImageFilter::class);
            $this->filter->addImageFilter($filter);
            $filters[] = $filter;
        }

        if ($filterSalt) {
            $filters[0]->method('getCacheSalt')
                ->willReturn($filterSalt);
        }

        $result = $this->filter->getCacheSalt($resource, []);
        $this->assertNotEmpty($result);
        $this->assertTrue(is_string($result));
        $this->assertNotContains($result, $previousResults);
        $previousResults[] = $result;
    }

    public function correctHashData() {
        return [
            ['http://phast.test', 123, 1],
            ['http://phast.test', 123],
            ['http://phast.test', 234],
            ['http://phast-1.test', 123],
            ['http://phast-1.test', 234],
            ['http://phast-1.test', 234, 2, 'some-salt'],
        ];
    }

    private function getMockFilter(Image $returnImage = null) {
        $returnImage = is_null($returnImage) ? $this->image : $returnImage;
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
