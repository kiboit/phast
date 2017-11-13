<?php

namespace Kibo\Phast\Services;

use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\Exceptions\UnauthorizedException;
use Kibo\Phast\HTTP\Request;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\ValueObjects\URL;
use PHPUnit\Framework\TestCase;

class ScriptsProxyServiceTest extends TestCase {

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
        $this->retriever = $this->createMock(Retriever::class);
        $signature = $this->createMock(ServiceSignature::class);
        $signature->method('verify')
            ->willReturnCallback(function ($token) {
                return $token == 'the-token';
            });
        $this->service = new ScriptsProxyService(
            $signature,
            ['~http://allowed\.com~'],
            $this->retriever
        );
    }

    public function testFetching() {
        $request = [
            'src' => 'http://allowed.com/the-script'
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

    public function testNoExceptionOnNotAllowedURLWithToken() {
        $request = ['src' => 'http://not-allowed.com/the-script', 'cacheMarker' => 123456789, 'token' => 'the-token'];
        $this->service->serve(Request::fromArray($request, []));
    }

    public function testExceptionOnNoResult() {
        $request = ['src' => 'http://allowed.com/the-script', 'cacheMarker' => 123456789];
        $this->retriever->expects($this->once())
            ->method('retrieve')
            ->willReturn(false);
        $this->expectException(ItemNotFoundException::class);
        $this->service->serve(Request::fromArray($request, []));
    }

}
