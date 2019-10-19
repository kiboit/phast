<?php
namespace Kibo\Phast\Filters\HTML\MinifyScripts;

use Kibo\Phast\Filters\HTML\HTMLFilterTestCase;

class FilterTest extends HTMLFilterTestCase {

    public function setUp() {
        parent::setUp();
        $this->filter = new Filter();
    }

    public function testMinifyScripts() {
        $html = '
            <script type=json>
                {
                    "hello": "w\u00f6rld",
                    "a/b": "<\/script>"
                }
            </script>
            <script>{ nope }</script>
        ';
        $actual = $this->applyFilter($html, true);
        $this->assertContains('<script type=json>{"hello":"wörld","a/b":"<\/script>"}</script>', $actual);
        $this->assertContains('<script>{nope}</script>', $actual);
    }

}
