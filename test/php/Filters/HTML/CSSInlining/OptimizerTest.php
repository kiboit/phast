<?php

namespace Kibo\Phast\Filters\HTML\CSSInlining;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\Filters\HTML\HTMLFilterTestCase;
use Kibo\Phast\Parsing\HTML\PCRETokenizer;

class OptimizerTest extends HTMLFilterTestCase {
    /**
     * @var Cache
     */
    private $cache;

    public function setUp(): void {
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

        $optimizer = $this->makeOptimizer();
        $firstCSSOptimized = $optimizer->optimizeCSS($firstCSS);
        $secondCSSOptimized = $optimizer->optimizeCSS($secondCSS);

        $this->assertStringContainsString('.class1', $firstCSSOptimized);
        $this->assertStringContainsString('red', $firstCSSOptimized);
        $this->assertStringNotContainsString('.class2', $firstCSSOptimized);
        $this->assertStringNotContainsString('green', $firstCSSOptimized);
        $this->assertStringNotContainsString('.class3', $firstCSSOptimized);
        $this->assertStringNotContainsString('blue', $firstCSSOptimized);

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

        $cssOptimized = $this->optimizeCSS($css);

        $this->assertStringContainsString('.class1', $cssOptimized);
        $this->assertStringContainsString('red', $cssOptimized);

        if ($shouldOptimize) {
            $this->assertStringNotContainsString('.class2', $cssOptimized);
            $this->assertStringNotContainsString('blue', $cssOptimized);
        } else {
            $this->assertStringContainsString('blue', $cssOptimized);
        }
    }

    public function selectorProvider() {
        return [
            [true,  'a > .class2'],
            [true,  'a ~ .class2'],
            [true,  'a + .class2'],
            [true,  '.class2 input[disabled]'],
            [false, 'input[disabled]'],
            [false, '.class2 a[rel*="class2"]'],
        ];
    }

    public function testOptimizeWithoutClasses() {
        $css = '
            .class1 { background: red; }
            span { background: blue; }
        ';

        $div = $this->makeElement('div', 'Hello, World!');
        $this->body->appendChild($div);

        $cssOptimized = $this->optimizeCSS($css);

        $this->assertStringNotContainsString('.class1', $cssOptimized);
        $this->assertStringContainsString('span', $cssOptimized);
    }

    /**
     * This is a regression test
     */
    public function testLargeCSS() {
        $css = file_get_contents(__DIR__ . '/../../../resources/large.css');
        $this->assertNotFalse($css);

        $optimized = $this->optimizeCSS($css);
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

        $cssOptimized = $this->optimizeCSS($css);

        $this->assertStringNotContainsString('.no', $cssOptimized);
        $this->assertStringContainsString('.yes', $cssOptimized);
        $this->assertStringContainsString('red', $cssOptimized);
    }

    /**
     * This is a regression test: our pattern would not match the first rule in
     * a media query, because it was preceded by '{' and thus ignored.
     */
    public function testOptimizeCSSInMediaQuery() {
        $css = '@media print{.hidden-print{display:none}}a{display:block;}';

        $optimizedCSS = $this->optimizeCSS($css);

        $this->assertEquals('a{display:block;}', $optimizedCSS);
    }

    public function testNotClass() {
        $css = '
            .cls *:not(.x) { font-weight: bold; }
        ';

        $div = $this->makeElement('div', '');
        $div->setAttribute('class', 'cls');
        $this->body->appendChild($div);

        $cssOptimized = $this->optimizeCSS($css);

        $this->assertStringContainsString('.cls', $cssOptimized);
    }

    public function testRemoveEmptyMediaQueries() {
        $css = '@media screen{.present{color:blue;}}' .
               '@media print and (max-width: 640px){.not-present{color:green;}}';

        $div = $this->makeElement('div', '');
        $div->setAttribute('class', 'present');
        $this->body->appendChild($div);

        $cssOptimized = $this->optimizeCSS($css);

        $this->assertEquals('@media screen{.present{color:blue;}}', $cssOptimized);
    }

    private function makeElement($tag, $content) {
        $el = $this->dom->createElement($tag);
        $el->textContent = $content;
        return $el;
    }

    /**
     * @return Optimizer
     */
    private function makeOptimizer() {
        $html = $this->dom->saveHTML();
        return new Optimizer((new PCRETokenizer())->tokenize($html), $this->cache);
    }

    /**
     * @param $css
     * @return string
     */
    private function optimizeCSS($css) {
        return $this->makeOptimizer()->optimizeCSS($css);
    }
}
