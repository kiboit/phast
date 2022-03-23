<?php

namespace Kibo\Phast\ValueObjects;

use Kibo\Phast\Common\ObjectifiedFunctions;
use PHPUnit\Framework\TestCase;

class PhastJavaScriptTest extends TestCase {
    public function testFromFile() {
        $funcs = new ObjectifiedFunctions();
        $filename = 'the-test';
        $script = 'var q = 123';
        $funcs->file_get_contents = function ($name) use ($filename, $script) {
            $this->assertEquals($filename, $name);
            return $script;
        };
        $js = PhastJavaScript::fromFile($filename, $funcs);
        $this->assertEquals($filename, $js->getFilename());
        $this->assertEquals('var q=123', $js->getContents());
        $this->assertEquals(substr(base64_encode(md5($script, true)), 0, 16), $js->getCacheSalt());
    }

    public function testFromString() {
        $js = PhastJavaScript::fromString('abc.js', 'var q = 123');
        $this->assertEquals('abc.js', $js->getFilename());
        $this->assertEquals('var q = 123', $js->getContents());
        $this->assertEquals(substr(base64_encode(md5('var q = 123', true)), 0, 16), $js->getCacheSalt());
    }
}
