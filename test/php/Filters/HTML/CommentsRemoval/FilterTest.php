<?php

namespace Kibo\Phast\Filters\HTML\CommentsRemoval;

use Kibo\Phast\Filters\HTML\HTMLFilterTestCase;

class FilterTest extends HTMLFilterTestCase {
    public function setUp(): void {
        parent::setUp();
        $this->filter = new Filter();
    }

    public function testCommentsRemoval() {
        $html = '<html><head><!-- Comment here --></head><body><!-- another here--></body></html>';
        $actual = $this->applyFilter($html, true);
        $expected = '<html><head></head><body></body></html>';
        $this->assertStringStartsWith($expected, $actual);
    }

    public function testLeavingMSConditionals() {
        $html = '<html><head><!--[if IE]>somethinf here<![endif]--></head><body></body></html>';
        $actual = $this->applyFilter($html, true);
        $this->assertStringStartsWith($html, $actual);
    }
}
