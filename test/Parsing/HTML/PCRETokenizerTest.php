<?php

namespace Kibo\Phast\Parsing\HTML;


use Kibo\Phast\Parsing\HTML\HTMLStreamElements\ClosingTag;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Element;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;

class PCRETokenizerTest extends \PHPUnit_Framework_TestCase {

    public function testSimpleDocument() {
        $html = "
            <!doctype html>
            <html>
            <head>
            <title>Hello, World!</title>
            </head>
            <body>
            </body>
            </html>
        ";

        $tokenizer = new PCRETokenizer();
        $tokens = $tokenizer->tokenize($html);

        $this->checkTokens([
            [Tag::class, '<!doctype html>'],
            [Tag::class, '<html>'],
            [Tag::class, '<head>'],
            [Tag::class, '<title>'],
            [Element::class, 'Hello, World!'],
            [ClosingTag::class, '</title>'],
            [ClosingTag::class, '</head>'],
            [Tag::class, '<body>'],
            [ClosingTag::class, '</body>'],
            [ClosingTag::class, '</html>']
        ], $tokens);
    }

    private function checkTokens(array $expected, \Traversable $actual) {
        foreach ($actual as $token) {
            if ($token instanceof Element && trim((string) $token) === '') {
                continue;
            }
            $this->assertNotEmpty($expected);
            $expectedToken = array_shift($expected);
            $this->assertEquals($expectedToken, [get_class($token), (string) $token]);
        }
        $this->assertEmpty($expected);
    }

}
