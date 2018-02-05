<?php

namespace Kibo\Phast\Common;


use Kibo\Phast\Cache\Cache;
use Kibo\Phast\ValueObjects\PhastJavaScript;
use PHPUnit\Framework\TestCase;

class PhastJavaScriptCompilerTest extends TestCase {

    private $cache;

    public function setUp() {
        parent::setUp();
        $this->cache = $this->createMock(Cache::class);
        $this->cache->method('get')
            ->willReturnCallback(function ($key, callable $cb) {
                return $cb();
            });
    }

    public function testCompileScripts() {
        $funcs1 = new ObjectifiedFunctions();
        $funcs1->file_get_contents = function () {
            return 'var    a;';
        };
        $funcs2 = new ObjectifiedFunctions();
        $funcs2->file_get_contents = function () {
            return 'var    b;';
        };
        $scripts = [new PhastJavaScript('f1', $funcs1), new PhastJavaScript('f2', $funcs2)];
        $compiled = $this->runCompiler($scripts);

        $expected = '(function(){(function(){var a;})();(function(){var b;})();})();';
        $this->assertEquals($expected, $compiled);
    }

    public function testCaching() {
        $keys = [];
        $this->cache = $this->createMock(Cache::class);
        $this->cache->expects($this->exactly(3))
            ->method('get')
            ->willReturnCallback(function ($key, callable $cb) use (&$keys) {
                $keys[] = $key;
                return 'cached';
            });

        $s1 = new PhastJavaScript('f1');
        $s2 = new PhastJavaScript('f2');

        $this->assertEquals('cached', $this->runCompiler([$s1, $s2]));
        $this->assertEquals('cached', $this->runCompiler([$s1, $s2]));
        $this->assertEquals('cached', $this->runCompiler([$s1]));

        $this->assertEquals($keys[0], $keys[1]);
        $this->assertNotEquals($keys[1], $keys[2]);
    }

    private function runCompiler(array $scripts) {
        $compiler = new PhastJavaScriptCompiler($this->cache);
        return $compiler->compileScripts($scripts);
    }
}
