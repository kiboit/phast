<?php

namespace Kibo\Phast\Filters\CSS\CSSMinifier;

use Kibo\Phast\ValueObjects\Resource;
use Kibo\Phast\ValueObjects\URL;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase {
    public function testMinifying() {
        $css = 'a-tag    sub-selector { prop: 12% }';
        $resource = Resource::makeWithContent(URL::fromString('http://phast.test'), $css);
        $minified = (new Filter())->apply($resource, []);
        $this->assertEquals('a-tag sub-selector{prop:12%}', $minified->getContent());
    }
}
