<?php

namespace Kibo\Phast\Filters\HTML\CSSDeferring;

use Kibo\Phast\Filters\HTML\HTMLFilterTestCase;

class FilterTest extends HTMLFilterTestCase {

    /**
     * @var Filter
     */
    private $filter;

    public function setUp() {
        parent::setUp();

        $this->filter = new Filter();
    }

    public function testDeferCSS() {
        $this->markTestSkipped('Not implemented');
        $style_link = $this->dom->createElement('link');
        $style_link->setAttribute('rel', 'stylesheet');
        $style_link->setAttribute('href', 'test.css');
        $this->head->appendChild($style_link);

        $other_link = $this->dom->createElement('link');
        $this->head->appendChild($other_link);

        $this->filter->transformHTMLDOM($this->dom);

        $links = $this->dom->getElementsByTagName('link');
        $scripts = $this->dom->getElementsByTagName('script');

        $this->assertEquals(1, $links->length);
        $this->assertSame($other_link, $links->item(0));

        $this->assertEquals(1, $scripts->length);
        $this->assertContains('test.css', $scripts->item(0)->textContent);
        $this->assertEquals('phast-link', $scripts->item(0)->getAttribute('type'));

        $scripts = $this->dom->getPhastJavaScripts();
        $this->assertCount(1, $scripts);
        $this->assertStringEndsWith('CSSDeferring/styles-loader.js', $scripts[0]->getFilename());
    }

    public function testDoNothing() {
        $other_link = $this->dom->createElement('link');
        $this->head->appendChild($other_link);

        $this->filter->transformHTMLDOM($this->dom);

        $links = $this->dom->getElementsByTagName('link');
        $scripts = $this->dom->getElementsByTagName('script');

        $this->assertEquals(1, $links->length);
        $this->assertSame($other_link, $links->item(0));

        $this->assertEquals(0, $scripts->length);
    }

}
