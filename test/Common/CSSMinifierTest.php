<?php

namespace Kibo\Phast\Common;

use PHPUnit\Framework\TestCase;

class CSSMinifierTest extends TestCase {

    public function testMinifying() {
        $css = 'a-tag    sub-selector { prop: 12% }';
        $minified = (new CSSMinifier())->minify($css);
        $this->assertEquals('a-tag sub-selector{prop:12%}', $minified);
    }

}
