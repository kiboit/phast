<?php

namespace Kibo\Phast\Filters\Image;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\Filters\Image\ImageImplementations\DummyImage;
use PHPUnit\Framework\TestCase;

class CachedCompositeImageFilterTest extends TestCase {

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $cache;

    /**
     * @var CachedCompositeImageFilter
     */
    private $filter;

    /**
     * @var array
     */
    private $request = ['height' => 'the-height', 'src' => 'the-src', 'width' => 'the-width'];

    public function setUp() {
        parent::setUp();
        $this->cache = $this->createMock(Cache::class);
        $this->filter = new CachedCompositeImageFilter($this->cache, $this->request);
    }

    public function testCorrectHash() {
        $filters = [
            $this->createMock(ImageFilter::class),
            $this->createMock(ImageFilter::class)
        ];
        $this->filter->addImageFilter($filters[1]);
        $this->filter->addImageFilter($filters[0]);
        sort($filters);
        $hash = md5(
            get_class($filters[0]) . get_class($filters[1])
            . $this->request['src'] . $this->request['width'] . $this->request['height']
        );
        $this->cache->expects($this->once())
            ->method('get')
            ->with($hash);
        $this->filter->apply(new DummyImage());
    }

    public function testReturningImageFromCache() {
        $originalImage = new DummyImage(200, 200);
        $originalImage->setImageString('non-filtered');
        $originalImage->setTransformationString('filtered');
        $originalImage->setType('the-type');

        $filter = $this->createMock(ImageFilter::class);
        $filter->expects($this->once())
            ->method('transformImage')
            ->with($originalImage)
            ->willReturn($originalImage->resize(100, 200));
        $this->filter->addImageFilter($filter);

        $cache = [];
        $this->cache->method('get')
            ->willReturnCallback(function ($key, $cb) use (&$cache) {
                if (isset ($cache[$key])) {
                    return $cache[$key];
                }
                $content = $cb();
                $cache[$key] = $content;
                return $content;
            });
        $notCached = $this->filter->apply($originalImage);
        $cached = $this->filter->apply($originalImage);

        $this->assertNotSame($notCached, $originalImage);
        $this->assertNotSame($cached, $originalImage);
        $this->assertNotSame($cached, $notCached);

        foreach ([$notCached, $cached] as $output) {
            $this->assertEquals('the-type', $output->getType());
            $this->assertEquals(100, $output->getWidth());
            $this->assertEquals(200, $output->getHeight());
        }

    }

}
