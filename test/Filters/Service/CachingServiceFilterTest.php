<?php

namespace Kibo\Phast\Filters\Service;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\Exceptions\CachedExceptionException;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\ValueObjects\Resource;
use Kibo\Phast\ValueObjects\URL;
use PHPUnit\Framework\TestCase;

class CachingServiceFilterTest extends TestCase {

    const LAST_MODIFICATION_TIME = 123456789;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $cache;

    private $cacheGetMethodConfig;

    /**
     * @var CachingServiceFilter
     */
    private $filter;

    /**
     * @var Resource
     */
    private $resource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $cachedServiceFilter;

    /**
     * @var int
     */
    private $modTime;

    public function setUp() {
        parent::setUp();

        $this->cache = $this->createMock(Cache::class);
        $this->cacheGetMethodConfig = $this->cache->expects($this->any());
        $this->cacheGetMethodConfig->method('get')
            ->willReturnCallback(function ($key, callable $cb) {
                return $cb();
            });
        $this->modTime = null;

        $retriever = $this->createMock(Retriever::class);
        $retriever->method('retrieve')
            ->willReturn('the-blob');
        $retriever->method('getLastModificationTime')
            ->willReturnCallback(function () {
                return is_null($this->modTime) ? self::LAST_MODIFICATION_TIME : $this->modTime;
            });

        $this->resource = Resource::makeWithRetriever(
            URL::fromString('http://cache.phast.test'),
            $retriever,
            'the-mime-type'
        );

        $this->cachedServiceFilter = $this->createMock(CachedResultServiceFilter::class);
        $this->cachedServiceFilter->method('apply')
            ->willReturnCallback(function (Resource $resource) {
                return $resource->withContent($resource->getContent());
            });
        $this->cachedServiceFilter->method('getCacheHash')
            ->with($this->resource, [])
            ->willReturn('the-hash');

        $this->filter = $this->makeFilter();
    }

    /**
     * @dataProvider correctTimeToCacheData
     */
    public function testCorrectCachingParameters($modTime, $expectedTtl) {
        $this->modTime = $modTime;
        $this->cacheGetMethodConfig->with('the-hash', $this->isType('callable'), $expectedTtl);
        $this->filter->apply($this->resource, []);
    }

    public function correctTimeToCacheData() {
        return [
            [null, 0],
            [0, 86400]
        ];
    }

    public function testReturningResourceFromCache() {
        $actual = $this->filter->apply($this->resource, []);
        $this->assertEquals($this->resource->getUrl(), $actual->getUrl());
        $this->assertEquals($this->resource->getMimeType(), $actual->getMimeType());
        $this->assertEquals($this->resource->getContent(), $actual->getContent());
    }

    public function testCachingExceptions() {
        $this->cachedServiceFilter->expects($this->once())
            ->method('apply')
            ->willThrowException(new \RuntimeException());
        $cache = [];
        $this->cache = $this->createMock(Cache::class);
        $this->cache->expects($this->exactly(2))
            ->method('get')
            ->willReturnCallback(function ($key, callable $cb) use (&$cache) {
                if (!isset ($cache[$key])) {
                    $cache[$key] = $cb();
                }
                return $cache[$key];
            });

        $thrown = 0;
        $this->filter = $this->makeFilter();
        try {
            $this->filter->apply($this->resource, []);
        } catch (CachedExceptionException $e) {
            $thrown++;
        }
        try {
            $this->filter->apply($this->resource, []);
        } catch (CachedExceptionException $e) {
            $thrown++;
        }

        $this->assertEquals(2, $thrown);
    }

    private function makeFilter() {
        return new CachingServiceFilter($this->cache, $this->cachedServiceFilter);
    }

}
