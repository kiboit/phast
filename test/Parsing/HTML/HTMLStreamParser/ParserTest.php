<?php

namespace Kibo\Phast\Parsing\HTML\HTMLStreamParser;


use Kibo\Phast\Parsing\HTML\HTMLStream;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\ClosingTag;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;
use Kibo\Phast\Parsing\HTML\HTMLStreamParser\ParserStates\AwaitingTag;
use Kibo\Phast\Parsing\HTML\StringInputStream;
use Masterminds\HTML5\Parser\Scanner;

class ParserTest extends ParserTestCase {

    public function testInitialState() {
        $this->assertInstanceOf(AwaitingTag::class, $this->parser->getState());
    }

    public function testReset() {
        $this->parser->setState($this->createMock(ParserState::class));
        $this->assertNotInstanceOf(AwaitingTag::class, $this->parser->getState());
        $this->parser->reset();
        $this->assertInstanceOf(AwaitingTag::class, $this->parser->getState());
    }

    public function testReturnsValueFromStartTag() {
        $state = $this->createMock(ParserState::class);
        $state->expects($this->once())
            ->method('startTag')
            ->willReturn('the-return-value');
        $this->parser->setState($state);
        $returnValue = $this->parser->startTag('tag', [], 0, 10);
        $this->assertEquals('the-return-value', $returnValue);
    }

    public function testHTMLParsing() {
        $html =  '<!doctype html><html charset="utf-8"><head><style>some-text-here</style></head>';
        $html .= '<body>some text <script>the-script</script> here too</body> </html>';
        $inputStream = new StringInputStream($html);
        $htmlStream = new HTMLStream();
        $parser = new Parser($htmlStream, $inputStream);
        $tokenizer = new Tokenizer(new Scanner($inputStream), $parser);
        $tokenizer->parse();

        $elements = $htmlStream->getAllElements();

        $expectedTokens = [
            '<!doctype html>', '<html charset="utf-8">',
            '<head>', '<style>some-text-here</style>',
            '</head>', '<body>', 'some text ',
            '<script>the-script</script>', ' here too',
            '</body>', ' ', '</html>'
        ];

        $this->assertCount(count($expectedTokens), $elements);

        for ($i = 0; $i < count($expectedTokens); $i++) {
            $this->assertEquals($expectedTokens[$i], $elements->item($i)->toString());
        }

        $this->assertInstanceOf(Tag::class, $elements->item(1));
        $this->assertEquals('html', $elements->item(1)->getTagName());
        $this->assertTrue($elements->item(1)->hasAttribute('charset'));
        $this->assertEquals('utf-8', $elements->item(1)->getAttribute('charset'));

        $this->assertInstanceOf(Tag::class, $elements->item(2));
        $this->assertEquals('head', $elements->item(2)->getTagName());

        $this->assertInstanceOf(Tag::class, $elements->item(3));
        $this->assertEquals('style', $elements->item(3)->getTagName());
        $this->assertEquals('some-text-here', $elements->item(3)->getTextContent());

        $this->assertInstanceOf(ClosingTag::class, $elements->item(4));
        $this->assertEquals('head', $elements->item(4)->getTagName());

        $this->assertInstanceOf(Tag::class, $elements->item(5));
        $this->assertEquals('body', $elements->item(5)->getTagName());

        $this->assertInstanceOf(Tag::class, $elements->item(7));
        $this->assertEquals('script', $elements->item(7)->getTagName());
        $this->assertEquals('the-script', $elements->item(7)->getTextContent());

        $this->assertInstanceOf(ClosingTag::class, $elements->item(9));
        $this->assertEquals('body', $elements->item(9)->getTagName());

        $this->assertInstanceOf(ClosingTag::class, $elements->item(11));
        $this->assertEquals('html', $elements->item(11)->getTagName());

    }

}
