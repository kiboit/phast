<?php

namespace Kibo\Phast\Parsing\HTML\HTMLStreamElements;

class TagTest extends \PHPUnit\Framework\TestCase {
    private $opening = '<sPaN claSS="SoMe\"Class\"">';

    private $text = 'Text';

    private $closing = '</SpAn>';

    public function testOriginalOutput() {
        $this->assertEquals(
            $this->opening . $this->text . $this->closing,
            $this->makeTheTag()->toString()
        );
    }

    public function testNotClosingWithOriginal() {
        $tag = new Tag('html');
        $tag->setOriginalString('<html>');
        $this->assertEquals('<html>', $tag->toString());
    }

    public function testGeneratingWithRemovedAttr() {
        $tag = $this->makeTheTag();
        $tag->removeAttribute('class');
        $expectedOpening = '<span>';
        $this->assertEquals(
            $expectedOpening . $this->text . $this->closing,
            $tag->toString()
        );
    }

    public function testGeneratingWithChangedAttr() {
        $tag = $this->makeTheTag();
        $tag->setAttribute('class', 'the-class<&');
        $expectedOpening = '<span class="the-class&lt;&amp;">';
        $this->assertEquals(
            $expectedOpening . $this->text . $this->closing,
            $tag->toString()
        );
    }

    public function testGeneratingWhenNoOriginal() {
        $tag = new Tag('span');
        $tag->setTextContent('the-text');
        $this->assertEquals('<span>the-text</span>', $tag->toString());

        $tag = new Tag('span');
        $this->assertEquals('<span></span>', $tag->toString());
    }

    public function testGenerateSelfClosingTag() {
        $tag = new Tag('img', ['/' => '']);
        $this->assertEquals('<img />', $tag->toString());
        $tag->setAttribute('src', 'test');
        $this->assertEquals('<img src="test" />', $tag->toString());
    }

    public function testNumericAttributes() {
        $tag = new Tag('img', ['0' => '0', '1' => '1']);
        $this->assertEquals('<img 0="0" 1="1">', $tag->toString());
        $tag->setAttribute('0', 'a');
        $this->assertEquals('<img 0="a" 1="1">', $tag->toString());
    }

    public function testCaseInsensitivity() {
        $tag = new Tag('SpAn', ['Class' => 'CamelCase']);
        $this->assertEquals('span', $tag->getTagName());
        $this->assertTrue($tag->hasAttribute('class'));
        $this->assertEquals('CamelCase', $tag->getAttribute('class'));
    }

    public function testGetAttributes() {
        $tag = new Tag('test', ['a' => '1', 'b' => '2']);
        $this->assertEquals(['a', 'b'], array_keys($tag->getAttributes()));
        $tag->setAttribute('c', '3');
        $this->assertEquals(['c', 'a', 'b'], array_keys($tag->getAttributes()));
        $tag->removeAttribute('a');
        $this->assertEquals(['c', 'b'], array_keys($tag->getAttributes()));
        $tag->removeAttribute('c');
        $this->assertEquals(['b'], array_keys($tag->getAttributes()));
    }

    private function makeTheTag() {
        $tag = new Tag('span', ['class' => 'SoMe\"Class\"']);
        $tag->setOriginalString($this->opening);
        $tag->setTextContent($this->text);
        return $tag->withClosingTag($this->closing);
    }

    public function testDoubleQuoteInAttribute() {
        $tag = new Tag('x');
        $tag->setAttribute('test', '"<>&\'');
        $this->assertStringContainsString('\'"<>&amp;&#039;\'', $tag->toString());
    }
}
