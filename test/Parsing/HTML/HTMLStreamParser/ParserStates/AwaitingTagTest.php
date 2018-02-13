<?php

namespace Kibo\Phast\Parsing\HTML\HTMLStreamParser\ParserStates;


use Kibo\Phast\Parsing\HTML\HTMLStreamElements\ClosingTag;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;
use Kibo\Phast\Parsing\HTML\HTMLStreamParser\ParserTestCase;
use Masterminds\HTML5\Elements;

class AwaitingTagTest extends ParserTestCase {

    /**
     * @var AwaitingTag
     */
    private $state;

    public function setUp() {
        parent::setUp();
        $this->state = new AwaitingTag($this->parser);
    }

    /**
     * @dataProvider getOpeningTagInputData
     */
    public function testOpeningTagInput($tagName, $expectedReturn) {
        $start = mt_rand(10, 40);
        $end = mt_rand(60, 80);
        $attr = 'offset-' . $start;
        $returned = $this->state->startTag($tagName, ['data-start' => $attr], $start, $end);

        $this->assertEquals($expectedReturn, $returned);

        $newState = $this->parser->getState();
        $this->assertInstanceOf(AwaitingTag::class, $newState);

        $elements = $this->htmlStream->getAllElements();
        $this->assertCount(1, $elements);

        /** @var Tag $tag */
        $tag = $elements[0];
        $this->assertInstanceOf(Tag::class, $tag);
        $this->assertEquals($tagName, $tag->getTagName());
        $this->assertTrue($tag->hasAttribute('data-start'));
        $this->assertEquals($attr, $tag->getAttribute('data-start'));
    }

    public function getOpeningTagInputData() {
        foreach (Elements::$html5 as $tagName => $tagType) {
            if (in_array($tagName, ['script', 'style'])) {
                continue;
            }
            yield [$tagName, $tagType];
        }
    }

    /**
     * @dataProvider getNextStateForStyleAndScriptData
     */
    public function testNextStateForStyleAndScript($tagName, $expectedReturn) {
        $returned = $this->state->startTag($tagName, [], 0, 15);

        $this->assertEquals($returned, $expectedReturn);

        $this->assertEmpty($this->htmlStream->getAllElements());

        /** @var ConstructingTextContainingTag $newState */
        $newState = $this->parser->getState();
        $this->assertInstanceOf(ConstructingTextContainingTag::class, $newState);

        /** @var Tag $tag */
        $tag = $newState->getStartTag();
        $this->assertInstanceOf(Tag::class, $tag);
        $this->assertEquals($tagName, $tag->getTagName());
    }

    public function getNextStateForStyleAndScriptData() {
        return [
            ['script', Elements::element('script')],
            ['style' , Elements::element('style')]
        ];
    }

    public function testAddingCloseTagsToStream() {
        $this->state->endTag('a', 20, 30);
        $newState = $this->parser->getState();
        $this->assertInstanceOf(AwaitingTag::class, $newState);

        $elements = $this->htmlStream->getAllElements();
        $this->assertCount(1, $elements);

        /** @var ClosingTag $element */
        $element = $elements[0];
        $this->assertEquals('a', $element->getTagName());
    }

}
