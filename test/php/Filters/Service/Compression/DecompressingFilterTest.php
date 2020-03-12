<?php

namespace Kibo\Phast\Filters\Service\Compression;

use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Exceptions\RuntimeException;
use Kibo\Phast\PhastTestCase;
use Kibo\Phast\ValueObjects\Resource;
use Kibo\Phast\ValueObjects\URL;

class DecompressingFilterTest extends PhastTestCase {
    private $funcs;

    /**
     * @var DecompressingFilter
     */
    private $filter;

    public function setUp() {
        parent::setUp();
        if (!function_exists('gzdecode')) {
            $this->markTestSkipped('gzdecode function does not exist');
        }
        $this->funcs = new ObjectifiedFunctions();
        $this->filter = new DecompressingFilter($this->funcs);
    }

    public function testDoNothingOnNonCompressedResource() {
        $resource = Resource::makeWithContent(URL::fromString(self::BASE_URL), 'the-content', 'text/text');
        $result = $this->filter->apply($resource, []);
        $this->assertSame($resource, $result);
    }

    /**
     * @dataProvider respectingHeadersData
     */
    public function testRespectingHeaders($header, $shouldDecompress) {
        $resource = Resource::makeWithContent(
            URL::fromString(self::BASE_URL),
            gzencode('the-content'),
            'text/text',
            'gzip'
        );
        $result = $this->filter->apply($resource, ['accept-encoding' => $header]);
        if ($shouldDecompress) {
            $this->assertEquals('the-content', $result->getContent());
            $this->assertEquals('text/text', $result->getMimeType());
            $this->assertEquals('identity', $result->getEncoding());
        } else {
            $this->assertSame($result, $resource);
        }
    }

    public function respectingHeadersData() {
        return [
            ['identity', true],
            ['identity, gzip', false],
            ['*', true],
        ];
    }

    public function testExceptionOnMissingFunction() {
        $this->funcs->function_exists = function ($name) {
            if ($name == 'gzdecode') {
                return false;
            }
            return true;
        };
        $resource = Resource::makeWithContent(URL::fromString(self::BASE_URL), 'the-content', 'text/text');
        $this->expectException(RuntimeException::class);
        $this->filter->apply($resource, []);
    }
}
