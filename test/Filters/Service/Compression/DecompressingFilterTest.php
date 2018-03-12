<?php

namespace Kibo\Phast\Filters\Service\Compression;


use Kibo\Phast\PhastTestCase;
use Kibo\Phast\ValueObjects\Resource;
use Kibo\Phast\ValueObjects\URL;

class DecompressingFilterTest extends PhastTestCase {

    /**
     * @var DecompressingFilter
     */
    private $filter;

    public function setUp() {
        parent::setUp();
        if (!function_exists('gzdecode')) {
            $this->markTestSkipped('gzdecode function does not exist');
        }
        $this->filter = new DecompressingFilter();
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
        $result = $this->filter->apply($resource, ['accept-encoding' => [$header]]);
        if ($shouldDecompress) {
            $this->assertEquals('the-content', $result->getContent());
        } else {
            $this->assertSame($result, $resource);
        }
    }

    public function respectingHeadersData() {
        return [
            ['identity', true],
            ['gzip', false],
            ['*', false]
        ];
    }

}
