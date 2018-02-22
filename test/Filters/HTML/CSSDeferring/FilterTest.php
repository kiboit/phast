<?php

namespace Kibo\Phast\Filters\HTML\CSSDeferring;

use Kibo\Phast\Filters\HTML\HTMLFilterTestCase;

class FilterTest extends HTMLFilterTestCase {

    public function setUp() {
        parent::setUp();
        $this->filter = new Filter();
    }

    public function testDeferCSS() {
        $styleLink = $this->dom->createElement('link');
        $styleLink->setAttribute('rel', 'stylesheet');
        $styleLink->setAttribute('href', 'test.css');
        $this->head->appendChild($styleLink);

        $otherLink = $this->makeMarkedElement('link');
        $this->head->appendChild($otherLink);

        $this->applyFilter();

        $scripts = $this->dom->getElementsByTagName('script');

        $this->assertMatchingElementExists($otherLink);

        $this->assertGreaterThan(0, $scripts->length);
        $this->assertContains('test.css', $scripts->item(0)->textContent);
        $this->assertEquals('phast-link', $scripts->item(0)->getAttribute('type'));


        $this->assertHasCompiled('CSSDeferring/styles-loader.js');

    }

    public function testDoNothing() {
        $otherLink = $this->makeMarkedElement('link');
        $this->head->appendChild($otherLink);

        $this->applyFilter();

        $this->assertMatchingElementExists($otherLink);
        $scripts = $this->body->getElementsByTagName('script');

        $this->assertEquals(0, $scripts->length);
    }

}
