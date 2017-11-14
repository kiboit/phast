<?php

namespace Kibo\Phast\Filters\Image;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\Exceptions\CachedExceptionException;
use Kibo\Phast\Filters\Image\ImageImplementations\DummyImage;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\ValueObjects\URL;
use PHPUnit\Framework\TestCase;

class CachedCompositeImageFilterTest extends TestCase {

    const LAST_MODIFICATION_TIME = 123456789;

    private $imageArr = [
        'width' => 100,
        'height' => 100,
        'type' => 'asd',
        'blob' => 'the-blob',
        'dataType' => 'image'
    ];

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $cache;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $retriever;

    /**
     * @var CachedCompositeImageFilter
     */
    private $filter;

    /**
     * @var array
     */
    private $request;

    public function setUp($modTime = null) {
        parent::setUp();
        $this->cache = $this->createMock(Cache::class);
        $this->request = ['src' => 'the-src'];
        $this->retriever = $this->createMock(Retriever::class);
        $this->retriever->method('getLastModificationTime')
            ->willReturnCallback(function (URL $url) use ($modTime) {
                $this->assertEquals('the-src', $url->getPath());
                return is_null($modTime) ? self::LAST_MODIFICATION_TIME : $modTime;
            });
        $this->filter = new CachedCompositeImageFilter($this->cache, $this->retriever, $this->request);
    }

    public function testCorrectTimeToCache() {
        $this->cache->expects($this->once())
            ->method('get')
            ->willReturnCallback(function ($key, callable $cb, $ttl) {
                $this->assertEquals(0, $ttl);
                return $this->imageArr;
            });
        $this->filter->apply(new DummyImage(), $this->request);

        $this->setUp(0);
        $this->cache->expects($this->once())
            ->method('get')
            ->willReturnCallback(function ($key, callable $cb, $ttl) {
                $this->assertEquals(86400, $ttl);
                return $this->imageArr;
            });
        $this->filter->apply(new DummyImage(), $this->request);
    }

    /**
     * @dataProvider correctHashData
     */
    public function testCorrectHash(array $params) {
        $request = array_merge($this->request, $params);
        $this->filter = new CachedCompositeImageFilter($this->cache, $this->retriever, $request);
        $filters = [
            $this->createMock(ImageFilter::class),
            $this->createMock(ImageFilter::class)
        ];
        $this->filter->addImageFilter($filters[1]);
        $this->filter->addImageFilter($filters[0]);

        $this->cache->expects($this->once())
            ->method('get')
            ->willReturnCallback(function ($key) {
                $this->checkHashKey($key);
                return $this->imageArr;
            });
        $this->filter->apply(new DummyImage(), $this->request);
    }

    public function correctHashData() {
        return [
            [['height' => 'the-height']],
            [['height' => 'the-height', 'width' => 'the-width']],
            [['height' => 'the-height', 'width' => 'the-width', 'preferredType' => 'the-type']],
            [['height' => 'the-height-1', 'width' => 'the-width-1', 'preferredType' => 'the-type-1']]
        ];
    }

    private function checkHashKey($key) {
        static $lastKey = null;
        $this->assertNotEquals($lastKey, $key);
        $lastKey = $key;
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
                    return json_decode($cache[$key], true);
                }
                $content = $cb();
                $cache[$key] = json_encode($content);
                return $content;
            });
        $notCached = $this->filter->apply($originalImage, $this->request);
        $cached = $this->filter->apply($originalImage, $this->request);

        $this->assertNotSame($notCached, $originalImage);
        $this->assertNotSame($cached, $originalImage);
        $this->assertNotSame($cached, $notCached);

        foreach ([$notCached, $cached] as $output) {
            $this->assertEquals('the-type', $output->getType());
            $this->assertEquals(100, $output->getWidth());
            $this->assertEquals(200, $output->getHeight());
            $this->assertEquals('filtered', $output->getAsString());
        }

    }

    public function testCachingExceptions() {
        $image = new DummyImage();
        $filter = $this->createMock(ImageFilter::class);
        $filter->expects($this->once())
            ->method('transformImage')
            ->with($image)
            ->willThrowException(new \RuntimeException('except'));
        $this->filter->addImageFilter($filter);

        $cache = [];
        $this->cache->expects($this->exactly(2))
            ->method('get')
            ->willReturnCallback(function ($key, callable $cb) use (&$cache) {
                if (!isset ($cache[$key])) {
                    $cache[$key] = $cb();
                }
                return $cache[$key];
            });

        $thrown = 0;
        try {
            $this->filter->apply($image, $this->request);
        } catch (CachedExceptionException $e) {
            $thrown++;
        }
        try {
            $this->filter->apply($image, $this->request);
        } catch (CachedExceptionException $e) {
            $thrown++;
        }

        $this->assertEquals(2, $thrown);

    }

}
