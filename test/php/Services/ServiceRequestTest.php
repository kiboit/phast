<?php
namespace Kibo\Phast\Services;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\Common\Base64url;
use Kibo\Phast\HTTP\Request;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\ValueObjects\URL;
use PHPUnit\Framework\TestCase;

class ServiceRequestTest extends TestCase {
    public function setUp(): void {
        parent::setUp();
        ServiceRequest::resetRequestState();
    }

    /**
     * @dataProvider getSerializeParamsTestData
     */
    public function testSerializeParams(array $params) {
        $request = (new ServiceRequest())->withParams($params);
        $expectedQuery = http_build_query($params);
        $ext = preg_replace('/^.*\./', '', $params['src']);
        if ($ext == 'php') {
            $ext = 'js';
        }
        $expectedPath = '/' . Base64url::encode($expectedQuery) . '.q.' . $ext;
        $this->checkRequest($request, $expectedQuery, $expectedPath);
    }

    public function getSerializeParamsTestData() {
        yield [['src' => '/images/file.png']];
        yield [['src' => '/images/file.php']];
        yield [['src' => 'http://example.com/path/file.jpeg']];
        yield [['src' => 'http://example.com///path////file.png']];
        yield [['src' => 'http://example.com/path/file-file.png']];
        yield [['src' => 'the-file.png', 'width' => 20]];
        yield [['src' => 'the-file.png', 'width' => 20, 'height' => 30]];
    }

    /**
     * @dataProvider getSerializeParamsAndURLTestData
     */
    public function testSerializeParamsAndURL($url, $params, $expectedQuery, $expectedPath) {
        $request = (new ServiceRequest())->withParams($params)->withUrl(URL::fromString($url));
        $this->checkRequest($request, $expectedQuery, $expectedPath);
    }

    public function getSerializeParamsAndURLTestData() {
        return [
            [
                'images.php',
                ['src' => 'http://example.com/the-image.png'],
                'images.php/__p__.png?src=http%3A%2F%2Fexample.com%2Fthe-image.png',
                'images.php/c3JjPWh0dHAlM0ElMkYlMkZleGFtcGxlLmNvbSUyRnRoZS1pbWFnZS5wbmc.q.png',
            ],
            [
                'images.php?param=some-value',
                ['src' => 'the-image.jpeg'],
                'images.php/__p__.jpeg?param=some-value&src=the-image.jpeg',
                'images.php/cGFyYW09c29tZS12YWx1ZSZzcmM9dGhlLWltYWdlLmpwZWc.q.jpeg',
            ],
            [
                'images-service/',
                ['src' => 'the-image.png'],
                'images-service/?src=the-image.png',
                'images-service/c3JjPXRoZS1pbWFnZS5wbmc.q.png',
            ],
            [
                'images.php?param=value&src=overridden',
                ['src' => 'image.png'],
                'images.php/__p__.png?param=value&src=image.png',
                'images.php/cGFyYW09dmFsdWUmc3JjPWltYWdlLnBuZw.q.png',
            ],
            [
                'images.php',
                ['src' => 'http://example.com/the-image.png?ver=1'],
                'images.php/__p__.png?src=http%3A%2F%2Fexample.com%2Fthe-image.png%3Fver%3D1',
                'images.php/c3JjPWh0dHAlM0ElMkYlMkZleGFtcGxlLmNvbSUyRnRoZS1pbWFnZS5wbmclM0Z2ZXIlM0Qx.q.png',
            ],
        ];
    }

    public function testFromHTTPRequest() {
        $pathInfo = '/http-3A-2F-2Fexample.com-2Fthe-2Dimage.png/key2=value2/__p__.js';
        $getParams = ['key3' => 'value3'];
        $expectedParams = [
            'src' => 'http://example.com/the-image.png',
            'key2' => 'value2',
            'key3' => 'value3',
        ];

        $httpRequest = Request::fromArray($getParams, ['PATH_INFO' => $pathInfo]);
        $serviceRequest = ServiceRequest::fromHTTPRequest($httpRequest);
        $this->assertEquals($expectedParams, $serviceRequest->getParams());
        $this->assertSame($httpRequest, $serviceRequest->getHTTPRequest());
    }

    public function testSigning() {
        $signature = new ServiceSignature($this->createMock(Cache::class));
        $signature->setIdentities('some-token');
        $request = (new ServiceRequest())->withParams(['width' => 10, 'src' => 'url'])->sign($signature);


        $this->assertTrue($request->verify($signature));

        $queryFormat = $request->serialize(ServiceRequest::FORMAT_QUERY);
        $get = [];
        parse_str($queryFormat, $get);
        $queryRequest = ServiceRequest::fromHTTPRequest(Request::fromArray($get, []));

        $pathFormat = $request->serialize(ServiceRequest::FORMAT_PATH);
        $pathRequest = ServiceRequest::fromHTTPRequest(Request::fromArray([], ['PATH_INFO' => $pathFormat]));

        $this->assertStringStartsWith('width=10&src=url&token=', $request->serialize(ServiceRequest::FORMAT_QUERY));
        $this->assertStringStartsWith('/' . Base64url::encode('width=10&src=url&token='), $request->serialize(ServiceRequest::FORMAT_PATH));

        $this->assertTrue($queryRequest->verify($signature));
        $this->assertTrue($pathRequest->verify($signature));

        $this->assertArrayNotHasKey('token', $queryRequest->getParams());
        $this->assertArrayNotHasKey('token', $pathRequest->getParams());

        $clonedRequest = $request->withParams(['key' => 'value']);
        $this->assertFalse($clonedRequest->verify($signature));

        $newRequest = new ServiceRequest();
        $this->assertFalse($newRequest->verify($signature));

        $signature2 = new ServiceSignature($this->createMock(Cache::class));
        $signature2->setIdentities('something-else');
        $this->assertFalse($request->verify($signature2));
        $this->assertFalse($queryRequest->verify($signature2));
        $this->assertFalse($pathRequest->verify($signature2));
    }

    public function testGettingSwitchesFromGet() {
        $get = ['phast' => 'images,-webp'];
        $httpRequest = Request::fromArray($get, []);
        $serviceRequest = ServiceRequest::fromHTTPRequest($httpRequest);

        $expected = [
            'images' => true,
            'webp' => false,
            'phast' => true,
            'diagnostics' => false,

        ];
        $this->assertTrue($serviceRequest->hasRequestSwitchesSet());
        $this->assertEquals($expected, $serviceRequest->getSwitches()->toArray());
    }

    public function testGettingSwitchesFromCookies() {
        $cookie = ['phast' => 'images,-jpeg,diagnostics'];
        $get =    ['phast' => '-images,-webp'];
        $httpRequest = Request::fromArray($get, [], $cookie);
        $serviceRequest = ServiceRequest::fromHTTPRequest($httpRequest);
        $switches = $serviceRequest->getSwitches();

        $this->assertTrue($serviceRequest->hasRequestSwitchesSet());
        $this->assertFalse($switches->isOn('jpeg'));
        $this->assertTrue($switches->isOn('diagnostics'));
        $this->assertFalse($switches->isOn('images'));
        $this->assertFalse($switches->isOn('webp'));
    }

    public function testGettingSwitchesFromPathInfo() {
        $pathInfo = '/phast=diagnostics,-2Dphast';
        $httpRequest = Request::fromArray([], ['PATH_INFO' => $pathInfo]);
        $serviceRequest = ServiceRequest::fromHTTPRequest($httpRequest);

        $expected = [
            'diagnostics' => true,
            'phast' => false,
        ];

        $this->assertTrue($serviceRequest->hasRequestSwitchesSet());
        $this->assertEquals($expected, $serviceRequest->getSwitches()->toArray());
    }

    public function testGettingDefaultSwitches() {
        $httpRequest = Request::fromArray([], []);
        $serviceRequest = ServiceRequest::fromHTTPRequest($httpRequest);
        $this->assertFalse($serviceRequest->hasRequestSwitchesSet());
        $this->assertEquals(
            ['phast' => true, 'diagnostics' => false],
            $serviceRequest->getSwitches()->toArray()
        );
    }

    public function testPropagatingSwitches() {
        $httpRequest = Request::fromArray(['phast' => 's1.s2.-s3'], []);
        $serviceRequest = ServiceRequest::fromHTTPRequest($httpRequest);
        $url = $serviceRequest->withUrl(URL::fromString('phast.php?service=images'))
                ->withParams(['k' => 'v'])
                ->serialize(ServiceRequest::FORMAT_QUERY);
        $this->assertStringContainsString('phast=s1.s2.-s3', $url);
    }

    public function testGeneratingRequestId() {
        $httpRequest = Request::fromArray([], []);
        $id1 = ServiceRequest::fromHTTPRequest($httpRequest)->getDocumentRequestId();
        $id2 = ServiceRequest::fromHTTPRequest($httpRequest)->getDocumentRequestId();

        $this->assertTrue((bool) preg_match('/^\d{1,9}$/', $id1));
        $this->assertTrue((bool) preg_match('/^\d{1,9}$/', $id2));
        $this->assertNotEquals($id1, $id2);
    }

    public function testPreservingRequestId() {
        $httpRequest = Request::fromArray([], []);
        $id1 = ServiceRequest::fromHTTPRequest($httpRequest)->getDocumentRequestId();
        $id2 = (new ServiceRequest())->getDocumentRequestId();
        $this->assertEquals($id1, $id2);
    }

    public function testGettingRequestIdFromHTTPRequest() {
        $httpRequest = Request::fromArray(['documentRequestId' => 'the-id'], []);
        $id = ServiceRequest::fromHTTPRequest($httpRequest)->getDocumentRequestId();
        $this->assertEquals('the-id', $id);
    }

    public function testPropagatingRequestId() {
        ServiceRequest::setDefaultSerializationMode(ServiceRequest::FORMAT_QUERY);

        $url = URL::fromString('phast.php?service=diagnostics');
        $url1 = (new ServiceRequest())->withUrl($url)->serialize();
        $this->assertStringNotContainsString('documentRequestId=', $url1);

        $httpRequest = Request::fromArray(['phast' => 'diagnostics'], []);
        $url2 = ServiceRequest::fromHTTPRequest($httpRequest)
                ->withUrl($url)
                ->serialize();
        $this->assertStringContainsString('documentRequestId=', $url2);

        $url3 = (new ServiceRequest())->withUrl($url)->serialize();
        $this->assertStringContainsString('documentRequestId=', $url3);

        $httpRequest = Request::fromArray(['phast' => ''], [], ['phast' => 'diagnostics']);
        $url4 = ServiceRequest::fromHTTPRequest($httpRequest)
            ->withUrl($url)
            ->serialize();
        $this->assertStringContainsString('documentRequestId=', $url4);
    }

    public function testSettingDefaultSerializationMode() {
        $url = URL::fromString('phast.php?p=v');
        $pathSerialization = (new ServiceRequest())->withUrl($url)->serialize();

        ServiceRequest::setDefaultSerializationMode(ServiceRequest::FORMAT_QUERY);
        $querySerialization = (new ServiceRequest())->withUrl($url)->serialize();

        $this->assertEquals('phast.php/cD12.q.js', $pathSerialization);
        $this->assertEquals((string) $url, $querySerialization);
    }

    private function checkRequest(ServiceRequest $request, $expectedQuery, $expectedPath) {
        $actualQuery = $request->serialize(ServiceRequest::FORMAT_QUERY);
        $actualPath = $request->serialize(ServiceRequest::FORMAT_PATH);

        $this->assertEquals($expectedQuery, $actualQuery);
        $this->assertEquals($expectedPath, $actualPath);

        $pathRequest = ServiceRequest::fromHTTPRequest(
            Request::fromArray([], ['PATH_INFO' => $actualPath])
        );
        $this->assertEquals($request->getAllParams(), $pathRequest->getParams());

        $queryUrl = strpos($actualQuery, '?') === false ? '?' . $actualQuery : $actualQuery;
        $queryRequest = ServiceRequest::fromHTTPRequest(
            Request::fromArray([], ['REQUEST_URI' => $queryUrl])
        );
        $this->assertEquals($request->getAllParams(), $queryRequest->getParams());
    }

    public function testHxxp() {
        $request = Request::fromArray(['src' => 'hxxp://yolo']);
        $serviceRequest = ServiceRequest::fromHTTPRequest($request);
        $this->assertEquals('http://yolo', $serviceRequest->getParams()['src']);

        $request = Request::fromArray(['src' => 'hxxps://yolo']);
        $serviceRequest = ServiceRequest::fromHTTPRequest($request);
        $this->assertEquals('https://yolo', $serviceRequest->getParams()['src']);
    }

    public function testLegacyPathInfo() {
        $pathInfo = '/the-2Dfile.png/width=20/height=30/__p__.png';
        $httpRequest = Request::fromArray(['get' => 'yes'], ['PATH_INFO' => $pathInfo]);
        $serviceRequest = ServiceRequest::fromHTTPRequest($httpRequest);
        $this->assertSame(
            [
                'get' => 'yes',
                'src' => 'the-file.png',
                'width' => '20',
                'height' => '30',
            ],
            $serviceRequest->getParams()
        );
    }

    public function testBase64PathInfo() {
        $queryString = 'a=1&a=2&b=3';
        $pathInfo = '/' . $this->insertPathSeparators(Base64url::encode($queryString) . '.q.js');
        $httpRequest = Request::fromArray(['get' => 'yes'], ['PATH_INFO' => $pathInfo]);
        $serviceRequest = ServiceRequest::fromHTTPRequest($httpRequest);
        $this->assertSame(
            [
                'get' => 'yes',
                'a' => '1',
                'b' => '3',
            ],
            $serviceRequest->getParams()
        );
        $this->assertSame(['1', '2'], $serviceRequest->getQuery()->getAll('a'));
    }

    private function insertPathSeparators($path) {
        return strrev(implode('/', str_split(strrev($path), 6)));
    }

    public function testSplittingLongFilenames() {
        $request = (new ServiceRequest())->withParams(['src' => str_repeat('x', 1000)]);
        $pathInfo = $request->serialize(ServiceRequest::FORMAT_PATH);
        $this->assertMatchesRegularExpression('~[a-z0-9_-]{255}~i', $pathInfo);
        $this->assertDoesNotMatchRegularExpression('~[a-z0-9_-]{256}~i', $pathInfo);
    }

    public function testRetinaSrc() {
        $httpRequest = Request::fromArray([], ['PATH_INFO' => '/-2Fimages-2Ffile.jpg/__p__@2x.jpg']);
        $serviceRequest = ServiceRequest::fromHTTPRequest($httpRequest);
        $this->assertSame(
            ['src' => '/images/file@2x.jpg'],
            $serviceRequest->getParams()
        );
    }

    public function testRetinaSrcSigning() {
        $signature = new ServiceSignature($this->createMock(Cache::class));
        $signature->setIdentities('some-token');

        $request = (new ServiceRequest())->withParams(['src' => 'url.jpg'])->sign($signature);

        $pathInfo = $request->serialize(ServiceRequest::FORMAT_PATH);
        $this->assertEquals('/c3JjPXVybC5qcGcmdG9rZW49ZGZhMWRjOTU2NjQwOTllZQ.q.jpg', $pathInfo);

        $pathRequest = ServiceRequest::fromHTTPRequest(Request::fromArray([], ['PATH_INFO' => $pathInfo]));
        $this->assertTrue($pathRequest->verify($signature));
        $this->assertEquals(['src' => 'url.jpg'], $pathRequest->getParams());

        $pathInfo = preg_replace('~\.jpg$~', '@2x.jpg', $pathInfo, -1, $count);
        $this->assertEquals(1, $count);
        $pathRequest = ServiceRequest::fromHTTPRequest(Request::fromArray([], ['PATH_INFO' => $pathInfo]));
        $this->assertTrue($pathRequest->verify($signature));
        $this->assertEquals(['src' => 'url@2x.jpg'], $pathRequest->getParams());
    }
}
