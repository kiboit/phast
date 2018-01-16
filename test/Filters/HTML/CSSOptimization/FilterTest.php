<?php

namespace Kibo\Phast\Filters\HTML\CSSOptimization;

use Kibo\Phast\Filters\HTML\HTMLFilterTestCase;

class FilterTest extends HTMLFilterTestCase {

    /**
     * @var Filter
     */
    private $filter;

    public function setUp() {
        parent::setUp();

        $this->filter = new Filter();
    }

    public function testOptimizingCSS() {
        $firstCSS = '
            .class1 { background: red; }
            .class2 { background: green; }
            .class3 { background: blue; }
        ';

        $secondCSS = '
            .class2 { background: yellow; }
        ';

        $this->head->appendChild($this->makeElement('style', $firstCSS));
        $this->head->appendChild($this->makeElement('style', $secondCSS));

        $div = $this->makeElement('div', 'Hello, World!');
        $div->setAttribute('class', 'some-class class1 another-class');
        $this->body->appendChild($div);

        $this->filter->transformHTMLDOM($this->dom);

        $styles = $this->getTheStyles();

        $this->assertCount(2, $styles);

        $this->assertContains('.class1', $styles[0]->textContent);
        $this->assertContains('red', $styles[0]->textContent);
        $this->assertNotContains('.class2', $styles[0]->textContent);
        $this->assertNotContains('green', $styles[0]->textContent);
        $this->assertNotContains('.class3', $styles[0]->textContent);
        $this->assertNotContains('blue', $styles[0]->textContent);

        $this->assertEquals('', $styles[1]->textContent);

        $this->assertSame($this->head, $styles[0]->parentNode);
        $this->assertSame($this->head, $styles[1]->parentNode);

        $scripts = $this->getTheScripts();

        $this->assertEquals(3, sizeof($scripts));

        $this->assertEquals($firstCSS, $scripts[0]->textContent);
        $this->assertEquals($secondCSS, $scripts[1]->textContent);

        $this->assertSame($this->body, $scripts[0]->parentNode);
        $this->assertSame($this->body, $scripts[1]->parentNode);
        $this->assertSame($this->body, $scripts[2]->parentNode);
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
        $this->assertEquals(2, sizeof($scripts));
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

    public function testOptimizeWithoutClasses() {
        $this->head->appendChild($this->makeElement('style', '
            .class1 { background: red; }
            span { background: blue; }
        '));

        $div = $this->makeElement('div', 'Hello, World!');
        $this->body->appendChild($div);

        $this->filter->transformHTMLDOM($this->dom);

        $styles = $this->getTheStyles();
        $this->assertNotContains('.class1', $styles[0]->textContent);
        $this->assertContains('span', $styles[0]->textContent);

        $scripts = $this->getTheScripts();
        $this->assertEquals(2, sizeof($scripts));
    }

    public function testDontInjectScript() {
        $this->filter->transformHTMLDOM($this->dom);

        $styles = $this->getTheStyles();
        $this->assertEquals(0, sizeof($styles));

        $scripts = $this->getTheScripts();
        $this->assertEquals(0, sizeof($scripts));
    }

    public function testLargeCSS() {
        $css = file_get_contents(__DIR__ . '/../../../resources/large.css');
        $this->assertNotFalse($css);

        $this->head->appendChild($this->makeElement('style', $css));
        $this->filter->transformHTMLDOM($this->dom);

        $styles = $this->getTheStyles();
        $this->assertEquals(1, sizeof($styles));

        $scripts = $this->getTheScripts();
        $this->assertEquals(2, sizeof($scripts));
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
