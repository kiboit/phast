<?php

namespace Kibo\Phast\Filters\CSS\CSSURLRewriter;

use Kibo\Phast\ValueObjects\Resource;
use Kibo\Phast\ValueObjects\URL;
use PHPUnit\Framework\TestCase;

class CSSURLRewriterTest extends TestCase {
    const BASE_URL = 'http://phast.test';

    /**
     * @dataProvider urlProvider
     * @param $input
     * @param $output
     */
    public function testRewriteRelativeURLs($input, $output) {
        $inputs = [
            "url($input)",
            "url('$input')",
            "url(\"$input\")",
            "src:url($input);src:url($input);",
            "url( '$input' )",
        ];

        $base = URL::fromString(self::BASE_URL);
        $outputs = array_map(function ($input) use ($base) {
            return (new Filter())->apply(
                Resource::makeWithContent(URL::fromString('/css/test.css')->withBase($base), $input),
                []
            )->getContent();
        }, $inputs);


        $this->assertEquals("url($output)", $outputs[0]);
        $this->assertEquals("url('$output')", $outputs[1]);
        $this->assertEquals("url(\"$output\")", $outputs[2]);
        $this->assertEquals("src:url($output);src:url($output);", $outputs[3]);
        $this->assertEquals("url( '$output' )", $outputs[4]);
    }

    public function urlProvider() {
        return [
            [
                self::BASE_URL . '/style.css',
                self::BASE_URL . '/style.css',
            ],
            [
                '/style.css',
                self::BASE_URL . '/style.css',
            ],
            [
                'style.css',
                self::BASE_URL . '/css/style.css',
            ],
            [
                'http://cross-site.org/css/style.css',
                'http://cross-site.org/css/style.css',
            ],
            [
                'data:abcd',
                'data:abcd',
            ],
            [
                '#test',
                '#test',
            ],
        ];
    }
}
