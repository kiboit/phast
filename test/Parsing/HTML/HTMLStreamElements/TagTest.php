<?php

namespace Kibo\Phast\Parsing\HTML\HTMLStreamElements;


class TagTest extends \PHPUnit_Framework_TestCase {

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

    private function makeTheTag() {
        $tag = new Tag('span', ['class' => 'SoMe\"Class\"']);
        $tag->setOriginalString($this->opening);
        $tag->setTextContent($this->text);
        return $tag->withClosingTag($this->closing);
    }

}
