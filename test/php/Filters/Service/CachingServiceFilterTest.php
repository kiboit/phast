<?php

namespace Kibo\Phast\Filters\Service;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\Exceptions\CachedExceptionException;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\ValueObjects\Resource;
use Kibo\Phast\ValueObjects\URL;
use PHPUnit\Framework\TestCase;

class CachingServiceFilterTest extends TestCase {
    private $cachedData;

    private $cacheKey;

    private $filterCallback;

    private $retriever;

    private $retrieverLastModTime;

    /**
     * @var CachingServiceFilter
     */
    private $filter;

    public function setUp(): void {
        parent::setUp();

        $this->cachedData = [];
        $this->cacheKey = 'the-cache-key';
        $this->filterCallback = null;
        $this->retrieverLastModTime = 123;

        $cache = $this->createMock(Cache::class);
        $cache->method('get')
            ->willReturnCallback(function ($key, callable $cb = null, $ttl = 0) {
                if (!isset($this->cachedData[$key])) {
                    if ($cb) {
                        $data = $cb();
                        $this->cachedData[$key] = ['data' => $data, 'ttl' => $ttl];
                        return $data;
                    }
                    return null;
                }
                return $this->cachedData[$key]['data'];
            });
        $cache->method('set')
            ->willReturnCallback(function ($key, $data, $ttl) {
                $this->cachedData[$key] = ['data' => $data, 'ttl' => $ttl];
            });


        $cachedFilter = $this->createMock(CachedResultServiceFilter::class);
        $cachedFilter->method('getCacheSalt')
            ->willReturnCallback(function () {
                return $this->cacheKey;
            });

        $cachedFilter->method('apply')
            ->willReturnCallback(function (Resource $resource, array $request) {
                if ($this->filterCallback) {
                    return call_user_func($this->filterCallback, $resource, $request);
                }
                return $resource->withContent($resource->getContent());
            });

        $this->retriever = $this->createMock(Retriever::class);
        $this->retriever->method('getCacheSalt')
            ->willReturnCallback(function () {
                return $this->retrieverLastModTime;
            });

        $this->filter = new CachingServiceFilter($cache, $cachedFilter, $this->retriever);
    }

    public function testReturningResourceFromFilter() {
        $resource = Resource::makeWithContent(URL::fromString('http://phast.test'), 'the-content', 'the-mime');
        $actual = $this->filter->apply($resource, []);
        $this->assertEquals($resource->getUrl(), $actual->getUrl());
        $this->assertEquals($resource->getMimeType(), $actual->getMimeType());
        $this->assertEquals($resource->getContent(), $actual->getContent());
    }

    public function testReturningResourceFromCache() {
        $this->filterCallback = function (Resource $resource) {
            static $timesCalled = 1;
            return $resource->withContent($timesCalled++);
        };
        $resource = Resource::makeWithContent(URL::fromString('http://phast.test'), 0, 'the-mime');
        $this->filter->apply($resource, []);
        $actual = $this->filter->apply($resource, []);
        $this->assertEquals(1, $actual->getContent());
        $this->assertEquals('the-mime', $actual->getMimeType());
    }

    public function testIgnoringCacheWhenDependencyIsOld() {
        $this->filterCallback = function (Resource $resource) {
            static $timesCalled = 1;
            return $resource->withContent($timesCalled++);
        };

        $dep1 = Resource::makeWithRetriever(URL::fromString('http://phast-test/dep1'), $this->retriever);
        $dep2 = Resource::makeWithRetriever(URL::fromString('http://phast-test/dep2'), $this->retriever);
        $resource = Resource::makeWithContent(URL::fromString('http://phast.test'), 'the-content', 'the-mime')
            ->withDependencies([$dep1, $dep2]);
        $this->filter->apply($resource, []);

        $this->retrieverLastModTime += 100;
        $actual = $this->filter->apply($resource, []);
        $this->assertEquals(2, $actual->getContent());
    }

    public function testCachingExceptions() {
        $this->filterCallback = function () use (&$thrown) {
            static $thrown = false;
            if ($thrown) {
                $this->fail('Thrown exception was not cached!');
            } else {
                $thrown = true;
                throw new \RuntimeException();
            }
        };

        $resource = Resource::makeWithContent(URL::fromString('http://phast.test'), 'the-content', 'the-mime');

        for ($i = 0; $i < 2; $i++) {
            $e = null;
            try {
                $this->filter->apply($resource, []);
            } catch (CachedExceptionException $e) {
            }
            $this->assertInstanceOf(CachedExceptionException::class, $e);
        }
    }
}
