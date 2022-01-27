<?php

namespace Kibo\Phast\Filters\HTML\PhastScriptsCompiler;

use Kibo\Phast\Filters\HTML\HTMLFilterTestCase;
use Kibo\Phast\Filters\HTML\HTMLPageContext;
use Kibo\Phast\Filters\HTML\HTMLStreamFilter;
use Kibo\Phast\ValueObjects\PhastJavaScript;

class FilterTest extends HTMLFilterTestCase {
    /**
     * @var Filter
     */
    protected $filter;

    private $addScripts = true;

    private $expectedCompiledScript = '<script data-phast-compiled-js-names="some-file">the-js</script>';

    public function setUp(): void {
        parent::setUp();

        $this->jsCompiler = $this->createMock(PhastJavaScriptCompiler::class);
        $this->jsCompiler->method('compileScriptsWithConfig')
            ->willReturn('the-js');

        $this->addScripts = true;
        $this->filter = $this->createMock(HTMLStreamFilter::class);
        $this->filter->method('transformElements')
            ->willReturnCallback(function (\Traversable $elements, HTMLPageContext $context) {
                foreach ($elements as $element) {
                    yield $element;
                }
                if ($this->addScripts) {
                    $context->addPhastJavascript(PhastJavaScript::fromString('some-file', ''));
                }
            });
    }

    public function testAddingBeforeLastBodyTag() {
        $html = '<html><head></head><body></body></body><span>some text</span></body></html>';
        $filtered = $this->applyFilter($html, true);
        $expected = '<html><head></head><body></body></body><span>some text</span>';
        $expected .= $this->expectedCompiledScript;
        $expected .= '</body></html>';
        $this->assertStringStartsWith($expected, $filtered);
    }

    public function testAddingAtTheEndOfStream() {
        $html = '<html><head>';
        $filtered = $this->applyFilter($html, true);
        $expected = $html . $this->expectedCompiledScript;
        $this->assertStringStartsWith($expected, $filtered);
    }

    public function testNotAddingWhenNothingToAdd() {
        $this->addScripts = false;
        $this->applyFilter();
        $this->assertHasNotCompiledScripts();
    }

    public function testAddNonceAttr() {
        $this->cspNonce = 'abcd';
        $this->applyFilter();
        $script = $this->body->getElementsByTagName('script')->item('0');
        $this->assertEquals('abcd', $script->getAttribute('nonce'));
    }
}
