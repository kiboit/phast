<?php

namespace Kibo\Phast\Services\Scripts;

use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\Exceptions\UnauthorizedException;
use Kibo\Phast\HTTP\Request;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\Services\ServiceFilter;
use Kibo\Phast\Services\ServiceRequest;
use Kibo\Phast\ValueObjects\Resource;
use Kibo\Phast\ValueObjects\URL;
use PHPUnit\Framework\TestCase;

class ServiceTest extends TestCase {
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $retriever;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $filter;

    /**
     * @var Service
     */
    private $service;

    /**
     * @var array
     */
    private $calledWithParams;

    private $returnResource;

    public function setUp() {
        parent::setUp();

        $this->calledWithParams = [];
        $this->returnResource = null;

        $this->retriever = $this->createMock(Retriever::class);
        $this->filter = $this->createMock(ServiceFilter::class);
        $this->filter->method('apply')
            ->willReturnCallback(function (Resource $resource, array $params = null) {
                if (isset($this->returnResource)) {
                    return $this->returnResource;
                }
                $this->calledWithParams = $params;
                return $resource;
            });
        $signature = $this->createMock(ServiceSignature::class);
        $signature->method('verify')
            ->willReturnCallback(function ($token) {
                return $token == 'the-token';
            });
        $this->service = new Service(
            $signature,
            ['~http://allowed\.com~'],
            $this->retriever,
            $this->filter,
            []
        );
    }

    public function testFetching() {
        $httpRequest = Request::fromArray(['src' => 'http://allowed.com/the-script'], []);
        $this->retriever->expects($this->once())
            ->method('retrieve')
            ->willReturnCallback(function (URL $url) {
                $this->assertEquals('http://allowed.com/the-script', (string) $url);
                return 'the-content';
            });
        $result = $this->service->serve(ServiceRequest::fromHTTPRequest($httpRequest));
        $this->assertEquals('the-content', $result->getContent());
    }

    public function testExceptionOnNotAllowedURL() {
        $this->expectException(UnauthorizedException::class);
        $request = ['src' => 'http://not-allowed.com/the-script', 'cacheMarker' => 123456789];
        $httpRequest = Request::fromArray($request, []);
        $this->service->serve(ServiceRequest::fromHTTPRequest($httpRequest));
    }

    public function testNoExceptionOnNotAllowedURLWithToken() {
        $request = ['src' => 'http://not-allowed.com/the-script', 'cacheMarker' => 123456789, 'token' => 'the-token'];
        $httpRequest = Request::fromArray($request, []);
        $this->service->serve(ServiceRequest::fromHTTPRequest($httpRequest));
    }

    public function testExceptionOnNoResult() {
        $request = ['src' => 'http://allowed.com/the-script', 'cacheMarker' => 123456789];
        $httpRequest = Request::fromArray($request, []);
        $this->retriever->expects($this->once())
            ->method('retrieve')
            ->willReturn(false);
        $this->expectException(ItemNotFoundException::class);
        $this->service->serve(ServiceRequest::fromHTTPRequest($httpRequest));
    }

    public function testParsingAcceptEncoding() {
        $encoding = 'deflate, gzip;q=1.0, *;q=0.5';
        $httpRequest = Request::fromArray(
            ['src' => 'http://allowed.com/the-script'],
            ['HTTP_ACCEPT_ENCODING' => $encoding]
        );
        $this->service->serve(ServiceRequest::fromHTTPRequest($httpRequest));
        $this->assertEquals($encoding, $this->calledWithParams['accept-encoding']);
    }

    /**
     * @dataProvider compressionHeadersData
     */
    public function testCompressionHeaders($resource, $expectedEncoding) {
        $this->returnResource = $resource;
        $request = Request::fromArray(['src' => 'http://allowed.com/the-script']);
        $response = $this->service->serve(ServiceRequest::fromHTTPRequest($request));
        if ($expectedEncoding === null) {
            $this->assertArrayNotHasKey('Content-Encoding', $response->getHeaders());
        } else {
            $this->assertArraySubset(['Content-Encoding' => $expectedEncoding], $response->getHeaders());
        }
    }

    public function compressionHeadersData() {
        yield [Resource::makeWithContent(URL::fromString(''), '', '', 'gzip'), 'gzip'];
        yield [Resource::makeWithContent(URL::fromString(''), '', ''), null];
    }
}
