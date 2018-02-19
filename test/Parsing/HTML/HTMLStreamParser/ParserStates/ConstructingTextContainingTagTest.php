<?php

namespace Kibo\Phast\Parsing\HTML\HTMLStreamParser\ParserStates;


use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;
use Kibo\Phast\Parsing\HTML\HTMLStreamParser\ParserTestCase;

class ConstructingTextContainingTagTest extends ParserTestCase {

    /**
     * @var ConstructingTextContainingTag
     */
    private $state;

    public function setUp() {
        parent::setUp();
        $openingTag = new Tag('style', ['class' => 'the-value']);
        $openingTag->setOriginalString('the-original-content');
        $this->state = new ConstructingTextContainingTag($this->parser, $openingTag);
        $this->parser->setState($this->state);
    }

    public function testNormalProcessing() {
        $this->state->text('some-text');

        $currentState = $this->parser->getState();
        $this->assertInstanceOf(ConstructingTextContainingTag::class, $currentState);
        $this->assertEmpty($this->htmlStream->getAllElementsTagCollection());

        $this->inputStream->method('getSubstring')
            ->with(100, 111)
            ->willReturn('the-end-tag');
        $this->state->endTag('style', 100, 111);
        $newState = $this->parser->getState();
        $this->assertInstanceOf(AwaitingTag::class, $newState);


        $elements = $this->htmlStream->getAllElementsTagCollection();
        $this->assertCount(1, $elements);


        /** @var Tag $tag */
        $tag = $elements[0];
        $this->assertInstanceOf(Tag::class, $tag);
        $this->assertEquals('style', $tag->getTagName());
        $this->assertFalse($tag->hasAttribute('src'));
        $this->assertTrue($tag->hasAttribute('class'));
        $this->assertEquals('the-value', $tag->getAttribute('class'));
        $this->assertEquals('some-text', $tag->getTextContent());
        $this->assertEquals('the-original-contentsome-textthe-end-tag', $tag->toString());

        $this->assertEquals(112, $this->parser->getCaretPosition());
    }

    public function testResettingOnWrongClosingTag() {
        $this->state->endTag('script', 10, 20);
        $newState = $this->parser->getState();
        $this->assertInstanceOf(AwaitingTag::class, $newState);
        $this->assertEmpty($this->htmlStream->getAllElementsTagCollection());
    }

}
