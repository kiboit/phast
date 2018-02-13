<?php

namespace Kibo\Phast\Parsing\HTML\HTMLStreamParser\ParserStates;


use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\TextContainingTag;
use Kibo\Phast\Parsing\HTML\HTMLStreamParser\ParserTestCase;

class ConstructingTextContainingTagTest extends ParserTestCase {

    /**
     * @var ConstructingTextContainingTag
     */
    private $state;

    public function setUp() {
        parent::setUp();
        $openingTag = new Tag('style', ['class' => 'the-value']);
        $this->state = new ConstructingTextContainingTag($this->parser, $openingTag);
        $this->parser->setState($this->state);
    }

    public function testNormalProcessing() {
        $this->state->text('some-text');

        $currentState = $this->parser->getState();
        $this->assertInstanceOf(ConstructingTextContainingTag::class, $currentState);
        $this->assertEmpty($this->htmlStream->getAllElements());

        $this->state->endTag('style', 100, 200);
        $newState = $this->parser->getState();
        $this->assertInstanceOf(AwaitingTag::class, $newState);

        $elements = $this->htmlStream->getAllElements();
        $this->assertCount(1, $elements);

        /** @var Tag $tag */
        $tag = $elements[0];
        $this->assertInstanceOf(Tag::class, $tag);
        $this->assertEquals('style', $tag->getTagName());
        $this->assertFalse($tag->hasAttribute('src'));
        $this->assertTrue($tag->hasAttribute('class'));
        $this->assertEquals('the-value', $tag->getAttribute('class'));
        $this->assertEquals('some-text', $tag->getTextContent());
    }

    public function testResettingOnWrongClosingTag() {
        $this->state->endTag('script', 'the-script');
        $newState = $this->parser->getState();
        $this->assertInstanceOf(AwaitingTag::class, $newState);
        $this->assertEmpty($this->htmlStream->getAllElements());
    }

}
