<?php

namespace Kibo\Phast;


use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\HTTP\Request;
use Kibo\Phast\HTTP\Response;

class PhastServicesTest extends PhastTestCase {

    const EXAMPLE_CONTENT = 'This is the content we have to send';

    /**
     * @var integer
     */
    private $responseCode;

    /**
     * @var string[]
     */
    private $responseHeaders;

    /**
     * @var string
     */
    private $responseContent;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var ObjectifiedFunctions
     */
    private $functions;

    public function setUp() {
        parent::setUp();

        $this->request = Request::fromArray([], ['HTTP_ACCEPT_ENCODING' => 'gzip, deflate']);
        $this->functions = new ObjectifiedFunctions();
        $this->functions->http_response_code = function ($code) {
            $this->responseCode = $code;
        };
        $this->functions->header = function ($header) {
            $this->responseHeaders[] = $header;
        };
    }


    public function testOutputString() {
        $response = new Response();
        $response->setCode(201);
        $response->setHeader('X-Test-running', 'it-is-running');
        $response->setHeader('X-Test-running-2', 'it-is-still-running');
        $response->setContent(self::EXAMPLE_CONTENT);

        $this->executeTest($response);

        $this->assertEquals(201, $this->responseCode);

        $this->assertContains('X-Test-running: it-is-running', $this->responseHeaders);
        $this->assertContains('X-Test-running-2: it-is-still-running', $this->responseHeaders);
        $this->assertNotEmpty($this->getETagHeaderValue());
        if (!function_exists('gzencode')) {
            $this->assertContains('Content-Encoding: gzip', $this->responseHeaders);
        }

        $this->assertEquals(self::EXAMPLE_CONTENT, $this->responseContent);
    }

    public function testETagGeneration() {
        $response = new Response();

        $pattern = '/^"[a-f0-9]{32}"$/';

        $this->executeTest($response);

        $empty = $this->getETagHeaderValue();
        $this->assertRegExp($pattern, $empty);

        $response->setHeader('Hello', 'World');
        $this->executeTest($response);
        $withHeader = $this->getETagHeaderValue();
        $this->assertRegExp($pattern, $withHeader);
        $this->assertNotEquals($empty, $withHeader);

        $response->setContent('Hey!');
        $this->executeTest($response);
        $withContent = $this->getETagHeaderValue();
        $this->assertRegExp($pattern, $withContent);
        $this->assertNotEquals($withHeader, $withContent);

        $streamableContent = [self::EXAMPLE_CONTENT];
        $response->setContent($streamableContent);
        $this->executeTest($response);
        $withStreamable1 = $this->getETagHeaderValue();
        $this->executeTest($response);
        $withStreamable2 = $this->getETagHeaderValue();
        $this->assertNotEquals($withStreamable1, $withStreamable2);
    }

    public function testStreamingContent() {
        $stream = function () {
            foreach (explode(' ', 'This is the content we have to send') as $value) {
                yield $value;
            }
        };
        $response = new Response();
        $response->setContent($stream());
        $this->executeTest($response);
        $this->assertEquals(
            str_replace(' ', '', self::EXAMPLE_CONTENT),
            $this->responseContent
        );
    }

    public function testNotZippingAlreadyZipped() {
        if (!function_exists('gzencode')) {
            $this->markTestSkipped('gzencode() does not exist');
        }
        $response = new Response();
        $response->setContent(gzencode(self::EXAMPLE_CONTENT));
        $response->setHeader('Content-Encoding', 'gzip');
        $this->executeTest($response);
        $this->assertEquals(self::EXAMPLE_CONTENT, $this->responseContent);
    }

    public function testNotSettingContentEncodingIfNoGzipLib() {
        $this->functions->stream_filter_append = function () {
            return false;
        };
        $response = new Response();
        $response->setContent(self::EXAMPLE_CONTENT);
        $this->executeTest($response);
        $this->assertEquals(self::EXAMPLE_CONTENT, $this->responseContent);
        $this->assertNotContains('Content-Encoding: gzip', $this->responseHeaders);
    }

    public function testNotZippingWhenNotRequested() {
        $this->request = Request::fromArray();
        $response = new Response();
        $response->setContent(self::EXAMPLE_CONTENT);
        $this->executeTest($response);
        $this->assertEquals(self::EXAMPLE_CONTENT, $this->responseContent);
        $this->assertNotContains('Content-Encoding: gzip', $this->responseHeaders);
    }

    public function testNotZippingWhenImage() {
        $response = new Response();
        $response->setHeader('Content-Type', 'image/png');
        $response->setContent(self::EXAMPLE_CONTENT);
        $this->executeTest($response);
        $this->assertEquals(self::EXAMPLE_CONTENT, $this->responseContent);
        $this->assertNotContains('Content-Encoding: gzip', $this->responseHeaders);
    }

    private function executeTest(Response $response) {
        $this->responseCode = null;
        $this->responseHeaders = [];
        $this->responseContent = null;
        ob_start();
        PhastServices::output($this->request, $response, $this->functions);
        if (in_array('Content-Encoding: gzip', $this->responseHeaders)) {
            $this->responseContent = gzdecode(ob_get_contents());
        } else {
            $this->responseContent = ob_get_contents();
        }
        ob_end_clean();
    }

    private function getETagHeaderValue() {
        $etag = false;
        foreach ($this->responseHeaders as $header) {
            $matches = [];
            if (preg_match('/^ETag: (.*)/', $header, $matches)) {
                $etag = $matches[1];
                break;
            }
        }
        return $etag;
    }

}
