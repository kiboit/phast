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
            ['css' => "url($input)", 'path' => '/css/test.css'],
            ['css' => "url('$input')", 'path' => '/css/test2.css'],
            ['css' => "url(\"$input\")", 'path' => '/css/test3.css'],
        ];

        $base = URL::fromString(self::BASE_URL);
        $outputs = array_map(function ($input) use ($base) {
            return (new Filter())->apply(
                Resource::makeWithContent(
                    URL::fromString($input['path'])->withBase($base),
                    $input['css']
                ),
                []
            )->getContent();
        }, $inputs);


        $this->assertEquals("url($output)", $outputs[0]);
        $this->assertEquals("url('$output')", $outputs[1]);
        $this->assertEquals("url(\"$output\")", $outputs[2]);
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
        ];
    }
}
