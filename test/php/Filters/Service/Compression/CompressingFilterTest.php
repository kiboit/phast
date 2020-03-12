<?php

namespace Kibo\Phast\Filters\Service\Compression;

use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Exceptions\RuntimeException;
use Kibo\Phast\PhastTestCase;
use Kibo\Phast\ValueObjects\Resource;
use Kibo\Phast\ValueObjects\URL;

class CompressingFilterTest extends PhastTestCase {
    private $resource;

    public function setUp() {
        parent::setUp();
        if (!function_exists('gzencode')) {
            $this->markTestSkipped('gzencode function not found');
        }
        $this->resource = Resource::makeWithContent(URL::fromString(self::BASE_URL), 'some-content', 'text/css');
    }

    public function testApply() {
        $resource = Resource::makeWithContent(URL::fromString(self::BASE_URL), 'some-content', 'text/css');
        $filter = new CompressingFilter();
        $compressed = $filter->apply($this->resource, []);
        $this->assertEquals(gzencode('some-content'), $compressed->getContent());
        $this->assertEquals($resource->getMimeType(), $compressed->getMimeType());
        $this->assertEquals('gzip', $compressed->getEncoding());
    }

    public function testExceptionOnMissingFunction() {
        $filter = new CompressingFilter($this->gzencodeWillNotExist());
        $this->expectException(RuntimeException::class);
        $filter->apply($this->resource, []);
    }

    public function testGeneratingCacheSalt() {
        $filter1 = new CompressingFilter();
        $filter2 = new CompressingFilter($this->gzencodeWillNotExist());
        $this->assertNotEquals(
            $filter1->getCacheSalt($this->resource, []),
            $filter2->getCacheSalt($this->resource, [])
        );
    }

    private function gzencodeWillNotExist() {
        $funcs = new ObjectifiedFunctions();
        $funcs->function_exists = function ($func) {
            if ($func == 'gzencode') {
                return false;
            }
            return true;
        };
        return $funcs;
    }
}
