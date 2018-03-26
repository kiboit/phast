<?php

namespace Kibo\Phast\Services\Bundler;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\HTTP\Response;
use Kibo\Phast\PhastTestCase;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\Services\ServiceFilter;
use Kibo\Phast\Services\ServiceRequest;
use Kibo\Phast\ValueObjects\Resource;
use Kibo\Phast\ValueObjects\URL;

class ServiceTest extends PhastTestCase {

    /**
     * @var ServiceSignature
     */
    private $signature;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $filter;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $retriever;

    /**
     * @var Service
     */
    private $service;

    public function setUp() {
        parent::setUp();
        $this->signature = new ServiceSignature($this->createMock(Cache::class));
        $this->signature->setIdentities('some-identity');

        $this->retriever = $this->createMock(Retriever::class);
        $this->filter = $this->createMock(ServiceFilter::class);

        $this->retriever->method('retrieve')
            ->willReturnCallback(function (URL $url) {
                $invalidUTFChar = chr(200);
                return $url->toString() . '-content' . $invalidUTFChar;
            });

        $this->filter->method('apply')
            ->willReturnCallback(function (Resource $resource, array $params) {
                $newContent = $resource->getContent() . '-filtered';
                if (isset ($params['p1'])) {
                    $newContent .= '-' . $params['p1'];
                }
                return $resource->withContent($newContent);
            });

        $this->service = new Service($this->signature, $this->retriever, $this->filter);
    }

    public function testBundlingMultipleResources() {
        $params = $this->makeParams([
            ['src' => 'file-1', 'p1' => 'pf0'],
            ['src' => 'file-2'],
            ['src' => 'file-3', 'p1' => 'pf1']
        ]);
        $content = $this->doRequest($params);

        foreach ($content as $idx => $item) {
            $this->assertTrue(is_object($item));
            $this->assertEquals(200, $item->status);

            $expectedContent = $params['src_' . $idx] . '-content-filtered';
            if (isset ($params['p1_' . $idx])) {
                $expectedContent .= '-' . $params['p1_' . $idx];
            }
            $this->assertEquals($expectedContent, $item->content);
        }
    }

    public function testErrorOnBadlySignedBundledRequest() {
        $params = $this->makeParams([
            ['src' => 'will-pass', 'ab' => 'gogo'],
            ['src' => 'will-not-pass', 's' => 'm'],
            ['src' => 'will-not-pass', 'd' => 'q']
        ]);

        $params['token_1'] = 'nonsence';
        unset ($params['token_2']);

        $content = $this->doRequest($params);

        $this->assertEquals(200, $content[0]->status);
        $this->assertEquals(401, $content[1]->status);
        $this->assertEquals(401, $content[2]->status);
    }

    public function testExceptionHandling() {
        $params = $this->makeParams([
            ['src' => 'not-found'],
            ['src' => 'critical']
        ]);
        $this->filter->method('apply')
            ->willReturnCallback(function (Resource $resource) {
                if ($resource->getUrl()->toString() == 'not-found') {
                    throw new ItemNotFoundException();
                }
                throw new \Exception();
            });

        $content = $this->doRequest($params);

        $this->assertEquals(404, $content[0]->status);
        $this->assertEquals(500, $content[1]->status);
    }

    public function test404OnMissingSrc() {
        $params = $this->makeParams([
            ['key' => 'val']
        ]);
        $content = $this->doRequest($params);

        $this->assertEquals(404, $content[0]->status);
    }

    public function testIgnoreMalformedParams() {
        $params = ['key' => 'val'];
        $request = (new ServiceRequest())->withParams($params);
        $this->service->serve($request);
    }

    private function doRequest(array $params) {
        $request = (new ServiceRequest())->withParams($params);
        $response = $this->service->serve($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getCode());

        $headers = $response->getHeaders();
        $this->assertArrayHasKey('Content-Type', $headers);

        $this->assertEquals('application/json', $headers['Content-Type']);
        $content = json_decode($response->getContent());
        $this->assertTrue(is_array($content));
        return $content;
    }

    private function makeParams(array $input) {
        foreach ($input as &$item) {
            $item['token'] = ServiceParams::fromArray($item)
                ->sign($this->signature)
                ->toArray()['token'];
        }
        $output = [];
        foreach ($input as $idx => $items) {
            foreach ($items as $key => $value) {
                $output["{$key}_$idx"] = $value;
            }
        }
        return $output;
    }

}
