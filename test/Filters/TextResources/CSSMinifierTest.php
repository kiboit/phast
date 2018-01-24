<?php

namespace Kibo\Phast\Filters\TextResources;

use Kibo\Phast\ValueObjects\URL;
use PHPUnit\Framework\TestCase;

class CSSMinifierTest extends TestCase {

    public function testMinifying() {
        $css = 'a-tag    sub-selector { prop: 12% }';
        $resource = new TextResource(URL::fromString('http://phast.test'), $css);
        $minified = (new CSSMinifier())->transform($resource);
        $this->assertEquals('a-tag sub-selector{prop:12%}', $minified->getContent());
    }

}
