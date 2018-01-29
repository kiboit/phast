<?php

namespace Kibo\Phast\Filters\Image\Composite;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\Exceptions\CachedExceptionException;
use Kibo\Phast\Filters\Service\CachedResultServiceFilter;
use Kibo\Phast\ValueObjects\Resource;
use Kibo\Phast\ValueObjects\URL;
use PHPUnit\Framework\TestCase;

class CachedFilterTest extends TestCase {

    const LAST_MODIFICATION_TIME = 123456789;

    /**
     * @var array
     */
    private $resourceArr;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $cache;

    /**
     * @var CachedFilter
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

    public function setUp($modTime = null) {
        parent::setUp();
        $this->resourceArr = [
            'url' => 'http://cache.phast.test',
            'mimeType' => 'asd',
            'blob' => base64_encode('the-blob'),
            'dataType' => 'resource'
        ];
        $this->cache = $this->createMock(Cache::class);
        $this->resource = $this->createMock(Resource::class);
        $this->resource
            ->method('getLastModificationTime')
            ->willReturn(is_null($modTime) ? self::LAST_MODIFICATION_TIME : $modTime);
        $this->cachedServiceFilter = $this->createMock(CachedResultServiceFilter::class);
        $this->filter = new CachedFilter($this->cache, $this->cachedServiceFilter);
    }

    public function testCorrectTimeToCache() {
        $this->cache->expects($this->once())
            ->method('get')
            ->willReturnCallback(function ($key, callable $cb, $ttl) {
                $this->assertEquals(0, $ttl);
                return $this->resourceArr;
            });
        $this->filter->apply($this->resource, []);

        $this->setUp(0);
        $this->cache->expects($this->once())
            ->method('get')
            ->willReturnCallback(function ($key, callable $cb, $ttl) {
                $this->assertEquals(86400, $ttl);
                return $this->resourceArr;
            });
        $this->filter->apply($this->resource, []);
    }

    public function testCorrectHash() {
        $this->cachedServiceFilter->expects($this->once())
            ->method('getCacheHash')
            ->with($this->resource, [])
            ->willReturn('the-hash');
        $this->cache->expects($this->once())
            ->method('get')
            ->willReturnCallback(function ($key) {
                $this->assertEquals('the-hash', $key);
                return $this->resourceArr;
            });
        $this->filter->apply($this->resource, []);
    }

    public function testReturningResourceFromCache() {
        $originalResource = Resource::makeWithContent(URL::fromString('http://phast.test'), 'mime1', 'the-content');
        $this->cache->expects($this->once())
            ->method('get')
            ->willReturn($this->resourceArr);
        $actual = $this->filter->apply($originalResource, []);
        $this->assertEquals($this->resourceArr['url'], $actual->getUrl());
        $this->assertEquals($this->resourceArr['mimeType'], $actual->getMimeType());
        $this->assertEquals(base64_decode($this->resourceArr['blob']), $actual->getContent());
    }

    public function testReturningFromFilter() {
        $originalResource = Resource::makeWithContent(URL::fromString('http://phast.test'), 'mime1', 'the-content');
        $this->cachedServiceFilter->expects($this->once())
            ->method('apply')
            ->with($originalResource, [])
            ->willReturn($originalResource);
        $this->cache->expects($this->once())
            ->method('get')
            ->willReturnCallback(function ($hash, callable $cb) {
                return $cb();
            });
        $actual = $this->filter->apply($originalResource, []);
        $this->assertSame($originalResource->getUrl()->toString(), $actual->getUrl()->toString());
        $this->assertSame($originalResource->getMimeType(), $actual->getMimeType());
        $this->assertSame($originalResource->getContent(), $actual->getContent());
    }

    public function testCachingExceptions() {
        $this->cachedServiceFilter->expects($this->once())
            ->method('apply')
            ->willThrowException(new \RuntimeException());
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

}
