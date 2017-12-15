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
        $style_link = $this->dom->createElement('link');
        $style_link->setAttribute('rel', 'stylesheet');
        $style_link->setAttribute('href', 'test.css');
        $this->head->appendChild($style_link);

        $other_link = $this->dom->createElement('link');
        $this->head->appendChild($other_link);

        $this->filter->transformHTMLDOM($this->dom);

        $links = iterator_to_array($this->dom->getElementsByTagName('link'));
        $scripts = iterator_to_array($this->dom->getElementsByTagName('script'));

        $this->assertEquals(1, sizeof($links));
        $this->assertSame($other_link, $links[0]);

        $this->assertEquals(2, sizeof($scripts));
        $this->assertContains('test.css', $scripts[0]->textContent);
        $this->assertNotContains('test.css', $scripts[1]->textContent);
    }

    public function testDoNothing() {
        $other_link = $this->dom->createElement('link');
        $this->head->appendChild($other_link);

        $this->filter->transformHTMLDOM($this->dom);

        $links = iterator_to_array($this->dom->getElementsByTagName('link'));
        $scripts = iterator_to_array($this->dom->getElementsByTagName('script'));

        $this->assertEquals(1, sizeof($links));
        $this->assertSame($other_link, $links[0]);

        $this->assertEquals(0, sizeof($scripts));
    }

}
