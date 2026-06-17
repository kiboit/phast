<?php

namespace Kibo\Phast\Services\Images;

use Kibo\Phast\Filters\Image\Image;
use Kibo\Phast\HTTP\Request;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\Services\ServiceFilter;
use Kibo\Phast\Services\ServiceRequest;
use PHPUnit\Framework\TestCase;

class ServiceTest extends TestCase {
    public function setUp(): void {
        parent::setUp();
        ServiceRequest::resetRequestState();
    }

    public function testPreferredTypeContainsAllAcceptedTypes() {
        $request = ServiceRequest::fromHTTPRequest(Request::fromArray(
            ['src' => 'image.jpg'],
            ['REQUEST_URI' => '/phast.php', 'HTTP_ACCEPT' => 'image/avif,image/webp,*/*']
        ));

        $params = $this->makeService()->exposeGetParams($request);

        $this->assertSame(Image::TYPE_AVIF . ',' . Image::TYPE_WEBP, $params['preferredType']);
        $this->assertTrue($params['varyAccept']);
    }

    public function testPreferredTypeContainsOnlyAcceptedTypes() {
        $request = ServiceRequest::fromHTTPRequest(Request::fromArray(
            ['src' => 'image.jpg'],
            ['REQUEST_URI' => '/phast.php', 'HTTP_ACCEPT' => 'image/webp,*/*']
        ));

        $params = $this->makeService()->exposeGetParams($request);

        $this->assertSame(Image::TYPE_WEBP, $params['preferredType']);
    }

    public function testPreferredTypeExcludesRejectedTypes() {
        $request = ServiceRequest::fromHTTPRequest(Request::fromArray(
            ['src' => 'image.jpg'],
            ['REQUEST_URI' => '/phast.php', 'HTTP_ACCEPT' => 'image/avif;q=0,image/webp,*/*']
        ));

        $params = $this->makeService()->exposeGetParams($request);

        $this->assertSame(Image::TYPE_WEBP, $params['preferredType']);
    }

    public function testCloudflareDoesNotSupportAcceptHeaderByDefault() {
        $request = ServiceRequest::fromHTTPRequest(Request::fromArray(
            ['src' => 'image.jpg'],
            [
                'REQUEST_URI' => '/phast.php',
                'HTTP_ACCEPT' => 'image/webp,*/*',
                'HTTP_CF_RAY' => '1234',
            ]
        ));

        $params = $this->makeService()->exposeGetParams($request);

        $this->assertArrayNotHasKey('preferredType', $params);
        $this->assertArrayNotHasKey('varyAccept', $params);
    }

    public function testCloudflareSupportsAcceptHeaderOptionAllowsAcceptHeader() {
        $request = ServiceRequest::fromHTTPRequest(Request::fromArray(
            ['src' => 'image.jpg'],
            [
                'REQUEST_URI' => '/phast.php',
                'HTTP_ACCEPT' => 'image/webp,*/*',
                'HTTP_CF_RAY' => '1234',
            ]
        ));

        $params = $this->makeService(['cloudflareSupportsAcceptHeader' => true])->exposeGetParams($request);

        $this->assertSame(Image::TYPE_WEBP, $params['preferredType']);
        $this->assertTrue($params['varyAccept']);
    }

    private function makeService(array $imageConfig = []) {
        return new TestableImageService(
            $this->createMock(ServiceSignature::class),
            [],
            $this->createMock(Retriever::class),
            $this->createMock(ServiceFilter::class),
            ['images' => array_merge(['api-mode' => false], $imageConfig)]
        );
    }
}

class TestableImageService extends Service {
    public function exposeGetParams(ServiceRequest $request) {
        return parent::getParams($request);
    }
}
