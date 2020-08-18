<?php

namespace Kibo\Phast\Filters\JavaScript\Minification;

use Kibo\Phast\PhastTestCase;
use Kibo\Phast\ValueObjects\Resource;
use Kibo\Phast\ValueObjects\URL;

class JSMinifierFilterTest extends PhastTestCase {
    public function testGetCacheSalt() {
        $filter1 = new JSMinifierFilter(false);
        $filter2 = new JSMinifierFilter(true);
        $resource = Resource::makeWithContent(URL::fromString(self::BASE_URL), 'the-content');
        $this->assertNotEquals($filter1->getCacheSalt($resource, []), $filter2->getCacheSalt($resource, []));
    }

    public function testControlCharacters() {
        $filter = new JSMinifierFilter(true);
        $resource = Resource::makeWithContent(URL::fromString(self::BASE_URL), " alert( '\x13' ); ");
        $result = $filter->apply($resource, []);
        $this->assertEquals("alert('\x13');", $result->getContent());
    }
}
