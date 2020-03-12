<?php

namespace Kibo\Phast\ValueObjects;

use Kibo\Phast\Common\ObjectifiedFunctions;
use PHPUnit\Framework\TestCase;

class PhastJavaScriptTest extends TestCase {
    public function testGetters() {
        $funcs = new ObjectifiedFunctions();
        $filename = 'the-test';
        $called = false;
        $funcs->file_get_contents = function ($name) use ($filename, &$called) {
            $this->assertEquals($filename, $name);
            $called = true;
            return 'the-content';
        };
        $js = PhastJavaScript::fromFile($filename, $funcs);
        $this->assertEquals($filename, $js->getFilename());
        $this->assertEquals('the-content', $js->getContents());
        $this->assertTrue($called);
    }
}
