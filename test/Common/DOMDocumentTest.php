<?php

namespace Kibo\Phast\Common;

use Kibo\Phast\ValueObjects\PhastJavaScript;
use Kibo\Phast\ValueObjects\URL;
use PHPUnit\Framework\TestCase;

class DOMDocumentTest extends TestCase {

    /**
     * @var DOMDocument
     */
    private $dom;

    public function setUp() {
        parent::setUp();
        $jsCompiler = $this->createMock(PhastJavaScriptCompiler::class);
        $jsCompiler->method('compileScriptsWithConfig')
            ->willReturn('compiled-js');
        $this->dom = DOMDocument::makeForLocation(URL::fromString('http://phast.test'), $jsCompiler);
    }

    public function testGetBaseURLNoBaseTag() {
        $html = '<html><head></head></html>';
        $this->dom->loadHTML($html);
        $this->assertEquals('http://phast.test', $this->dom->getBaseURL()->toString());
    }

    public function testGetBaseURLWithBaseTag() {
        $html = '<html><head><base href="/the-base"></head></html>';
        $this->dom->loadHTML($html);
        $this->assertEquals('http://phast.test/the-base', $this->dom->getBaseURL()->toString());
    }

    public function testGetBaseURLWithBaseTagWithoutHref() {
        $html = '<html><head><base></head></html>';
        $this->dom->loadHTML($html);
        $this->assertEquals('http://phast.test', $this->dom->getBaseURL()->toString());
    }

    public function testAddGetJavaScripts() {
        $script1 = new PhastJavaScript('file1');
        $script2 = new PhastJavaScript('file2');
        $this->dom->addPhastJavaScript($script1);
        $this->dom->addPhastJavaScript($script2);
        $scripts = $this->dom->getPhastJavaScripts();
        $this->assertSame($script1, $scripts[0]);
        $this->assertSame($script2, $scripts[1]);
    }

    public function testSerializeToHTML5() {
        $this->markTestSkipped('Not implemented');
        $original = '<?ins v>';
        $original .= '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
        $original .= '<html><head></head><body>the-body</body></html><div>some-div</div>';
        $this->dom->loadHTML($original);
        $serialized = $this->dom->serializeToHTML5();
        $expected = "<!doctype html><html><head></head><body>the-body</body></html>";
        $expected .= "<html><div>some-div</div></html>";
        $this->assertEquals($expected, str_replace("\n", '', $serialized));
    }

    public function testAddingPhastJavaScripts() {
        $this->markTestSkipped('Not implemented');
        $html = '<html><head></head><body></body></html>';
        $this->dom->loadHTML($html);
        $this->dom->addPhastJavaScript(new PhastJavaScript('f1'));
        $serialized = $this->dom->serializeToHTML5();
        $expected = '<!doctype html><html><head></head><body><script>compiled-js</script></body></html>';
        $this->assertEquals($expected, str_replace("\n", '', $serialized));
    }

}
