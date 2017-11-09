<?php

namespace Kibo\Phast\Services;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\Exceptions\UnauthorizedException;
use Kibo\Phast\HTTP\Request;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\ValueObjects\URL;
use PHPUnit\Framework\TestCase;

class ScriptsProxyServiceTest extends TestCase {

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $cache;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $retriever;

    /**
     * @var ScriptsProxyService
     */
    private $service;

    public function setUp() {
        parent::setUp();
        $this->cache = $this->createMock(Cache::class);
        $this->retriever = $this->createMock(Retriever::class);
        $this->service = new ScriptsProxyService($this->retriever, $this->cache, ['~http://allowed\.com~']);
    }

    public function testFetching() {
        $this->useTransparentCache();
        $request = [
            'src' => 'http://allowed.com/the-script',
            'cacheMarker' => 123456789
        ];
        $this->retriever->expects($this->once())
            ->method('retrieve')
            ->willReturnCallback(function (URL $url) {
                $this->assertEquals('http://allowed.com/the-script', (string)$url);
                return 'the-content';
            });
        $result = $this->service->serve(Request::fromArray($request, []));
        $this->assertEquals('the-content', $result->getContent());
    }

    public function testExceptionOnNotAllowedURL() {
        $this->expectException(UnauthorizedException::class);
        $request = ['src' => 'http://not-allowed.com/the-script', 'cacheMarker' => 123456789];
        $this->service->serve(Request::fromArray($request, []));
    }

    public function testExceptionOnNoResult() {
        $this->useTransparentCache();
        $request = ['src' => 'http://allowed.com/the-script', 'cacheMarker' => 123456789];
        $this->retriever->expects($this->once())
            ->method('retrieve')
            ->willReturn(false);
        $this->expectException(ItemNotFoundException::class);
        $this->service->serve(Request::fromArray($request, []));
    }

    public function testCachingOnRequestedSrc() {
        $request = ['src' => 'http://allowed.com/the-script', 'cacheMarker' => 123456789];
        $this->cache->expects($this->once())
            ->method('get')
            ->with('http://allowed.com/the-script123456789');
        $this->service->serve(Request::fromArray($request, []));
    }

    private function useTransparentCache() {
        $this->cache->expects($this->once())
            ->method('get')
            ->willReturnCallback(function ($key, callable $cb) {
                return $cb();
            });
    }

}
