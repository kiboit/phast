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
     * @dataProvider getTagInputData
     */
    public function testTagInput($tagName, $expectedReturn) {
        $returned = $this->state->startTag($tagName, ['data-attr' => 'val'], 0, 20);

        $this->assertEquals($expectedReturn, $returned);

        $this->assertEquals(21, $this->parser->getCaretPosition());

        $newState = $this->parser->getState();
        $this->assertInstanceOf(AwaitingTag::class, $newState);

        $elements = $this->htmlStream->getAllElementsTagCollection();
        $this->assertCount(1, $elements);

        /** @var Tag $tag */
        $tag = $elements[0];
        $this->assertInstanceOf(Tag::class, $tag);
        $this->assertEquals($tagName, $tag->getTagName());
        $this->assertTrue($tag->hasAttribute('data-attr'));
        $this->assertEquals('val', $tag->getAttribute('data-attr'));
    }

    public function getTagInputData() {
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
        $returned = $this->state->startTag($tagName, [], 10, 20);

        $this->assertEquals($returned, $expectedReturn);

        $this->assertEquals(0, $this->parser->getCaretPosition());

        $this->assertEmpty($this->htmlStream->getAllElementsTagCollection());

        /** @var ConstructingTextContainingTag $newState */
        $newState = $this->parser->getState();
        $this->assertInstanceOf(ConstructingTextContainingTag::class, $newState);

        /** @var Tag $tag */
        $tag = $newState->getTag();
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
        $this->state->endTag('a', 10, 20);
        $newState = $this->parser->getState();
        $this->assertInstanceOf(AwaitingTag::class, $newState);

        $this->assertEquals(21, $this->parser->getCaretPosition());

        $elements = $this->htmlStream->getAllElementsTagCollection();
        $this->assertCount(1, $elements);

        /** @var ClosingTag $element */
        $element = $elements[0];
        $this->assertEquals('a', $element->getTagName());
    }

    public function testAddingNonTags() {
        $this->inputStream
            ->method('getSubString')
            ->willReturnMap([
                [0, 10, 'tag-1'],
                [11, 19, 'text-1'],
                [20, 30, 'tag-2'],
                [31, null, 'text-2']
            ]);
        $this->parser->startTag('span', [], 0, 10);
        $this->parser->endTag('span', 20, 30);
        $this->parser->eof();

        $elements = $this->htmlStream->getAllElementsTagCollection();
        $this->assertEquals(4, $elements->count());

        $this->assertInstanceOf(Tag::class, $elements->item(0));
        $this->assertNotInstanceOf(Tag::class, $elements->item(1));
        $this->assertInstanceOf(ClosingTag::class, $elements->item(2));
        $this->assertNotInstanceOf(Tag::class, $elements->item(3));

        $this->assertEquals('tag-1', $elements->item(0)->toString());
        $this->assertEquals('text-1', $elements->item(1)->toString());
        $this->assertEquals('tag-2', $elements->item(2)->toString());
        $this->assertEquals('text-2', $elements->item(3)->toString());
    }

    public function testAddingNonTagsBeforeNextState() {
        $this->inputStream
            ->method('getSubString')
            ->willReturnMap([
                [0, 9, 'the-text-1'],
                [10, 20, 'the-tag']
            ]);
        $this->parser->startTag('style', [], 10, 20);

        $elements = $this->htmlStream->getAllElementsTagCollection();
        $this->assertEquals(1, $elements->count());
        $this->assertNotInstanceOf(Tag::class, $elements->item(0));
        $this->assertEquals('the-text-1', $elements->item(0)->toString());
        $this->assertEquals(10, $this->parser->getCaretPosition());

        /** @var ConstructingTextContainingTag $newState */
        $newState = $this->parser->getState();
        $this->assertEquals('the-tag', $newState->getTag()->toString());
    }

    public function testSettingOriginalOnTextContainingTags() {
        $this->inputStream
            ->method('getSubString')
            ->willReturnMap([
                [0, 0, ''],
                [0, 10, 'the-tag']
            ]);

        $this->parser->startTag('style', [], 0, 10);

        /** @var ConstructingTextContainingTag $newState */
        $newState = $this->parser->getState();
        $this->assertEquals('the-tag', $newState->getTag()->toString());
    }
}
