<?php

namespace Kibo\Phast\Filters\HTML\DelayedIFrameLoading;

use Kibo\Phast\Filters\HTML\HTMLFilterTestCase;

class FilterTest extends HTMLFilterTestCase {
    public function setUp(): void {
        parent::setUp();
        $this->filter = new Filter();
    }

    public function testRewriting() {
        $iframe = $this->makeMarkedElement('iframe');
        $iframe->setAttribute('src', 'http://example.com');
        $this->body->appendChild($iframe);

        $this->applyFilter();

        $iframe = $this->getMatchingElement($iframe);

        $this->assertEquals('lazy', $iframe->getAttribute('loading'));
    }
}
