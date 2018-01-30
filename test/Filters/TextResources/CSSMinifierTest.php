<?php

namespace Kibo\Phast\Filters\TextResources;

use Kibo\Phast\ValueObjects\Resource;
use Kibo\Phast\ValueObjects\URL;
use PHPUnit\Framework\TestCase;

class CSSMinifierTest extends TestCase {

    public function testMinifying() {
        $css = 'a-tag    sub-selector { prop: 12% }';
        $resource = Resource::makeWithContent(URL::fromString('http://phast.test'), $css);
        $minified = (new CSSMinifier())->apply($resource, []);
        $this->assertEquals('a-tag sub-selector{prop:12%}', $minified->getContent());
    }

}
