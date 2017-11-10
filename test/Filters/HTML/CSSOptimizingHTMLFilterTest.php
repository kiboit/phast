<?php

namespace Kibo\Phast\Filters\HTML;

use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\ValueObjects\URL;

class CSSOptimizingHTMLFilterTest extends HTMLFilterTestCase {

    private $files;

    /**
     * @var CSSOptimizingHTMLFilter
     */
    private $filter;

    public function setUp() {
        parent::setUp();

        $this->filter = new CSSOptimizingHTMLFilter();
    }

    public function testOptimizingCSS() {
        $this->head->appendChild($this->makeElement('style', '
            .class1 { background: red; }
            .class2 { background: green; }
            .class3 { background: blue; }
        '));

        $this->head->appendChild($this->makeElement('style', '
            .class2 { background: yellow; }
        '));

        $div = $this->makeElement('div', 'Hello, World!');
        $div->setAttribute('class', 'some-class class1 another-class');
        $this->body->appendChild($div);

        $this->filter->transformHTMLDOM($this->dom);

        $styles = $this->getTheStyles();

        $this->assertCount(4, $styles);

        $this->assertContains('.class1', $styles[0]->textContent);
        $this->assertContains('red', $styles[0]->textContent);
        $this->assertNotContains('.class2', $styles[0]->textContent);
        $this->assertNotContains('green', $styles[0]->textContent);
        $this->assertNotContains('.class3', $styles[0]->textContent);
        $this->assertNotContains('blue', $styles[0]->textContent);

        $this->assertEquals('', $styles[1]->textContent);

        $this->assertContains('.class1', $styles[2]->textContent);
        $this->assertContains('.class2', $styles[2]->textContent);
        $this->assertContains('.class3', $styles[2]->textContent);

        $this->assertSame($this->head, $styles[0]->parentNode);
        $this->assertSame($this->head, $styles[1]->parentNode);
        $this->assertSame($this->body, $styles[2]->parentNode);
        $this->assertSame($this->body, $styles[3]->parentNode);

        $scripts = $this->getTheScripts();
        $this->assertEquals(1, sizeof($scripts));
        $this->assertSame($this->body->lastChild, $scripts[0]);
    }

    /**
     * @dataProvider selectorProvider
     */
    public function testOptimizeSelectors($shouldOptimize, $selector) {
        $this->head->appendChild($this->makeElement('style', '
            .class1 { background: red; }
            ' . $selector . ' { background: blue; }
        '));

        $div = $this->makeElement('div', 'Hello, World!');
        $div->setAttribute('class', 'some-class class1 another-class');
        $this->body->appendChild($div);

        $this->filter->transformHTMLDOM($this->dom);

        $styles = $this->getTheStyles();

        $this->assertContains('.class1', $styles[0]->textContent);
        $this->assertContains('red', $styles[0]->textContent);

        if ($shouldOptimize) {
            $this->assertNotContains('.class2', $styles[0]->textContent);
            $this->assertNotContains('blue', $styles[0]->textContent);
        } else {
            $this->assertContains('blue', $styles[0]->textContent);
        }

        $scripts = $this->getTheScripts();
        $this->assertEquals(1, sizeof($scripts));
    }

    public function selectorProvider() {
        return [
            [true,  'a > .class2'],
            [true,  'a ~ .class2'],
            [true,  'a + .class2'],
            [true,  '.class2 input[disabled]'],
            [false, 'input[disabled]'],
            [false, '.class2 a[rel*="class2"]']
        ];
    }

    public function testDontInjectScript() {
        $this->filter->transformHTMLDOM($this->dom);

        $styles = $this->getTheStyles();
        $this->assertEquals(0, sizeof($styles));

        $scripts = $this->getTheScripts();
        $this->assertEquals(0, sizeof($scripts));
    }

    public function testLargeCSS() {
        $css = file_get_contents(__DIR__ . '/../../resources/large.css');
        $this->assertNotFalse($css);

        $this->head->appendChild($this->makeElement('style', $css));
        $this->filter->transformHTMLDOM($this->dom);

        $styles = $this->getTheStyles();
        $this->assertEquals(2, sizeof($styles));
    }

    /**
     * @return \DOMElement[]
     */
    private function getTheStyles() {
        return iterator_to_array($this->dom->getElementsByTagName('style'));
    }

    /**
     * @return \DOMElement[]
     */
    private function getTheScripts() {
        return iterator_to_array($this->dom->getElementsByTagName('script'));
    }

    private function makeElement($tag, $content) {
        $el = $this->dom->createElement($tag);
        $el->textContent = $content;
        return $el;
    }

}
