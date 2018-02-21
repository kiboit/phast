<?php

namespace Kibo\Phast\Filters\HTML\PhastScriptsCompiler;


use Kibo\Phast\Common\PhastJavaScriptCompiler;
use Kibo\Phast\Filters\HTML\HTMLPageContext;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\ClosingTag;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Element;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;
use Kibo\Phast\ValueObjects\PhastJavaScript;
use Kibo\Phast\ValueObjects\URL;

class FilterTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $compiler;

    /**
     * @var HTMLPageContext
     */
    private $context;

    /**
     * @var Filter
     */
    private $filter;

    private $compiledScriptsText = 'the-js';

    public function setUp() {
        $this->compiler = $this->createMock(PhastJavaScriptCompiler::class);
        $this->compiler->method('compileScriptsWithConfig')
            ->willReturn($this->compiledScriptsText);
        $this->filter = new Filter($this->compiler);
        $this->context = new HTMLPageContext(URL::fromString('http://phast.test'));
    }

    public function testAddingBeforeFirstBodyTag() {
        $elements = [
            new Tag('html'),
            new Tag('head'),
            new ClosingTag('head'),
            new Tag('body'),
            new ClosingTag('body'),
            new ClosingTag('body'),
            new ClosingTag('html')
        ];

        $actual = $this->execute($elements);
        $this->assertCount(count($elements) + 1, $actual);
        $this->checkIsTag('body', $actual[3]);
        $this->checkIsCompiledScript($actual[4]);
        $this->checkIsClosingTag('body', $actual[5]);
        $this->checkIsClosingTag('body', $actual[6]);
    }

    public function testAddingAtTheEndOfStream() {
        $elements = [
            new Tag('html'),
            new Tag('head')
        ];

        $actual = $this->execute($elements);
        $this->assertCount(3, $actual);
        $this->checkIsCompiledScript($actual[2]);
    }

    public function testNotAddingWhenNothingToAdd() {
        $elements = new \ArrayIterator([new Tag('body'), new ClosingTag('body')]);

        /** @var Element $actual */
        $actual = iterator_to_array($this->filter->transformElements($elements, $this->context));
        $this->assertCount(2, $actual);
        $this->checkIsTag('body', $actual[0]);
        $this->checkIsClosingTag('body', $actual[1]);
    }

    public function testBuffering() {
        $generator = function () {
            $elements = [new ClosingTag('body'), new ClosingTag('body')];
            foreach ($elements as $element) {
                yield $element;
            }
            $this->context->addPhastJavascript(new PhastJavaScript('some-file'));
        };

        /** @var Element $actual */
        $actual = iterator_to_array($this->filter->transformElements($generator(), $this->context));
        $this->checkIsCompiledScript($actual[0]);
        $this->checkIsClosingTag('body', $actual[1]);
        $this->checkIsClosingTag('body', $actual[2]);
    }

    /**
     * @param array $elements
     * @return  Tag[]
     */
    private function execute(array $elements) {
        $this->context->addPhastJavascript(new PhastJavaScript('some-file'));
        $iterator = new \ArrayIterator($elements);
        return iterator_to_array($this->filter->transformElements($iterator, $this->context));
    }

    /**
     * @param $name
     * @param Tag $actual
     */
    private function checkIsTag($name, Element $actual) {
        $this->assertInstanceOf(Tag::class, $actual);
        $this->assertEquals($name, $actual->getTagName());
    }

    /**
     * @param Tag $actual
     */
    private function checkIsCompiledScript(Element $actual) {
        $this->checkIsTag('script', $actual);
        $this->assertEquals($this->compiledScriptsText, $actual->getTextContent());
    }

    /**
     * @param $name
     * @param ClosingTag $actual
     */
    private function checkIsClosingTag($name, Element $actual) {
        $this->assertInstanceOf(ClosingTag::class, $actual);
        $this->assertEquals($name, $actual->getTagname());
    }

}
