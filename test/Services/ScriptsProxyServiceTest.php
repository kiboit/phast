<?php

namespace Kibo\Phast\Services;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\HTTP\Request;
use Kibo\Phast\Security\ServiceSignature;
use PHPUnit\Framework\TestCase;

class ScriptsProxyServiceTest extends TestCase {

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $cache;

    /**
     * @var ObjectifiedFunctions
     */
    private $functions;

    /**
     * @var ScriptsProxyService
     */
    private $service;

    public function setUp() {
        parent::setUp();
        $this->cache = $this->createMock(Cache::class);
        $signature = $this->createMock(ServiceSignature::class);
        $signature->expects($this->once())
            ->method('verify')
            ->willReturn(true);
        $this->functions = new ObjectifiedFunctions();
        $this->service = new ScriptsProxyService($signature, $this->cache, $this->functions);
    }

    public function testFetching() {
        $this->useTransparentCache();
        $request = [
            'src' => 'the-script',
            'cacheMarker' => 123456789,
            'token' => 'token'
        ];
        $this->functions->file_get_contents = function ($url) {
            $this->assertEquals('the-script', $url);
            return 'the-content';
        };
        $result = $this->service->serve(Request::fromArray($request, []));
        $this->assertEquals('the-content', $result);
    }

    public function testExceptionOnNoResult() {
        $this->useTransparentCache();
        $request = ['src' => 'the-script', 'cacheMarker' => 123456789, 'token' => 'token'];
        $this->functions->file_get_contents = function () {
            return false;
        };
        $this->expectException(ItemNotFoundException::class);
        $this->service->serve(Request::fromArray($request, []));
    }

    public function testCachingOnRequestedSrc() {
        $request = ['src' => 'the-script', 'cacheMarker' => 123456789, 'token' => 'token'];
        $this->cache->expects($this->once())
            ->method('get')
            ->with('the-script123456789');
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
