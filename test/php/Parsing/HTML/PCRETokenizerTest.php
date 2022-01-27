<?php

namespace Kibo\Phast\Parsing\HTML;

use Kibo\Phast\Parsing\HTML\HTMLStreamElements\ClosingTag;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Junk;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;

class PCRETokenizerTest extends \PHPUnit\Framework\TestCase {
    public function testSimpleDocument() {
        $html = '
            <!doctype html>
            <html>
            <head>
            <title>Hello, World!</title>
            </head>
            <body>
            </body>
            </html>
        ';

        $tokenizer = new PCRETokenizer();
        $tokens = $tokenizer->tokenize($html);

        $this->checkTokens([
            [Tag::class, '<!doctype html>'],
            [Tag::class, '<html>'],
            [Tag::class, '<head>'],
            [Tag::class, '<title>'],
            [Junk::class, 'Hello, World!'],
            [ClosingTag::class, '</title>'],
            [ClosingTag::class, '</head>'],
            [Tag::class, '<body>'],
            [ClosingTag::class, '</body>'],
            [ClosingTag::class, '</html>'],
        ], $tokens);
    }

    private function checkTokens(array $expected, \Traversable $actual) {
        foreach ($actual as $token) {
            if ($token instanceof Junk && trim((string) $token) === '') {
                continue;
            }
            $this->assertNotEmpty($expected);
            $expectedToken = array_shift($expected);
            $this->assertEquals($expectedToken, [get_class($token), (string) $token]);
        }
        $this->assertEmpty($expected);
    }

    public function testAttributeStyles() {
        $html = '<div a=1 b="2" c=\'3\' d = 4 e = "5" ==6 f=`7`>';

        $tag = $this->tokenizeSingleTag($html);

        $this->assertEquals('1', $tag->getAttribute('a'));
        $this->assertEquals('2', $tag->getAttribute('b'));
        $this->assertEquals('3', $tag->getAttribute('c'));
        $this->assertEquals('4', $tag->getAttribute('d'));
        $this->assertEquals('5', $tag->getAttribute('e'));
        $this->assertEquals('6', $tag->getAttribute('='));
        $this->assertEquals('`7`', $tag->getAttribute('f'));
    }

    public function testSelfClosingTag() {
        $html = '<img src=hello />';
        $tag = $this->tokenizeSingleTag($html);
        $this->assertEquals('hello', $tag->getAttribute('src'));
        $this->assertTrue($tag->hasAttribute('/'));
    }

    public function testSlashAttribute() {
        $html = '<img /=hey>';
        $tag = $this->tokenizeSingleTag($html);
        $this->assertEquals('hey', $tag->getAttribute('/'));
    }

    public function testJoinedAttributes() {
        $html = '<div a="1"b=2>';

        $tag = $this->tokenizeSingleTag($html);

        $this->assertEquals('1', $tag->getAttribute('a'));
        $this->assertEquals('2', $tag->getAttribute('b'));
    }

    public function testGettingLastText() {
        $html = '<html><style>text</style></html> final text';

        $elements = iterator_to_array((new PCRETokenizer())->tokenize($html));
        $last = array_pop($elements);
        $this->assertEquals(' final text', $last->toString());
    }

    public function testMalformedScriptAndStyle() {
        $html = '<style>text</tag></style><script>text</tag></script>';

        /** @var Tag[] $elements */
        $elements = iterator_to_array((new PCRETokenizer())->tokenize($html));

        $this->assertCount(2, $elements);
        $this->assertInstanceOf(Tag::class, $elements[0]);
        $this->assertInstanceOf(Tag::class, $elements[1]);

        $this->assertEquals('style', $elements[0]->getTagName());
        $this->assertEquals('text</tag>', $elements[0]->getTextContent());

        $this->assertEquals('script', $elements[1]->getTagName());
        $this->assertEquals('text</tag>', $elements[1]->getTextContent());
    }

    public function testLiteralZeroTextElement() {
        $html = '<div>0</div>';
        $elements = iterator_to_array((new PCRETokenizer())->tokenize($html));
        $this->assertCount(3, $elements);
    }

    public function testDuplicateAttributes() {
        $html = '<div class=first class=second>';

        $tag = $this->tokenizeSingleTag($html);

        $this->assertEquals('first', $tag->getAttribute('class'));

        // Force the reading of remaining attributes
        $this->assertNull($tag->getAttribute('nope'));

        $this->assertEquals('first', $tag->getAttribute('class'));
    }

    public function testWhitespace() {
        $html = ' ';
        $elements = iterator_to_array((new PCRETokenizer())->tokenize($html), false);
        $this->assertCount(1, $elements);
        $this->assertSame(' ', $elements[0]->toString());
    }

    /**
     * @param $html
     * @return Tag
     */
    private function tokenizeSingleTag($html) {
        $tokenizer = new PCRETokenizer();
        $tokens = iterator_to_array($tokenizer->tokenize($html));

        $this->assertCount(1, $tokens);

        /** @var Tag $tag */
        $tag = $tokens[0];

        $this->assertInstanceOf(Tag::class, $tag);

        return $tag;
    }
}
