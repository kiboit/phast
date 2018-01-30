<?php

namespace Kibo\Phast\Filters\HTML\CSSInlining;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\Filters\HTML\HTMLFilterTestCase;

class OptimizerTest extends HTMLFilterTestCase {

    /**
     * @var Cache
     */
    private $cache;

    public function setUp() {
        parent::setUp();
        $this->cache = $this->createMock(Cache::class);
        $this->cache->expects($this->atLeast(1))
            ->method('get')
            ->willReturnCallback(function ($key, callable $cb) {
                return $cb();
            });
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

        $div = $this->makeElement('div', 'Hello, World!');
        $div->setAttribute('class', 'some-class class1 another-class');
        $this->body->appendChild($div);

        $optimizer = new Optimizer($this->dom, $this->cache);
        $firstCSSOptimized = $optimizer->optimizeCSS($firstCSS);
        $secondCSSOptimized = $optimizer->optimizeCSS($secondCSS);

        $this->assertContains('.class1', $firstCSSOptimized);
        $this->assertContains('red', $firstCSSOptimized);
        $this->assertNotContains('.class2', $firstCSSOptimized);
        $this->assertNotContains('green', $firstCSSOptimized);
        $this->assertNotContains('.class3', $firstCSSOptimized);
        $this->assertNotContains('blue', $firstCSSOptimized);

        $this->assertEquals('', $secondCSSOptimized);
    }

    /**
     * @dataProvider selectorProvider
     */
    public function testOptimizeSelectors($shouldOptimize, $selector) {
        $css = '
            .class1 { background: red; }
            ' . $selector . ' { background: blue; }
        ';

        $div = $this->makeElement('div', 'Hello, World!');
        $div->setAttribute('class', 'some-class class1 another-class');
        $this->body->appendChild($div);

        $cssOptimized = (new Optimizer($this->dom, $this->cache))->optimizeCSS($css);

        $this->assertContains('.class1', $cssOptimized);
        $this->assertContains('red', $cssOptimized);

        if ($shouldOptimize) {
            $this->assertNotContains('.class2', $cssOptimized);
            $this->assertNotContains('blue', $cssOptimized);
        } else {
            $this->assertContains('blue', $cssOptimized);
        }
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
        $css = '
            .class1 { background: red; }
            span { background: blue; }
        ';

        $div = $this->makeElement('div', 'Hello, World!');
        $this->body->appendChild($div);

        $cssOptimized = (new Optimizer($this->dom, $this->cache))->optimizeCSS($css);

        $this->assertNotContains('.class1', $cssOptimized);
        $this->assertContains('span', $cssOptimized);
    }

    /**
     * This is a regression test
     */
    public function testLargeCSS() {
        $css = file_get_contents(__DIR__ . '/../../../resources/large.css');
        $this->assertNotFalse($css);

        $optimized = (new Optimizer($this->dom, $this->cache))->optimizeCSS($css);
        $this->assertNotEmpty($optimized);
    }

    /**
     * This is a regression test for a bug that caused selectors to get removed
     * if they followed an optimizable selector. (Eg, .non-existent, .existent)
     */
    public function testMixedSelectors() {
        $css = '
            .no, .yes { background: red; }
        ';

        $div = $this->makeElement('div', 'Hello, World!');
        $div->setAttribute('class', 'yes');

        $this->body->appendChild($div);

        $cssOptimized = (new Optimizer($this->dom))->optimizeCSS($css);

        $this->assertNotContains('.no', $cssOptimized);
        $this->assertContains('.yes', $cssOptimized);
        $this->assertContains('red', $cssOptimized);
    }

    private function makeElement($tag, $content) {
        $el = $this->dom->createElement($tag);
        $el->textContent = $content;
        return $el;
    }

}
