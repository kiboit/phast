<?php

namespace Kibo\Phast\Filters\HTML;

use Kibo\Phast\ValueObjects\URL;

class CSSRearrangementHTMLFilterTest extends RearrangementHTMLFilterTestCase {

    /**
     * @var CSSRearrangementHTMLFilter
     */
    protected $filter;

    public function setUp() {
        parent::setUp();
        $this->filter = new CSSRearrangementHTMLFilter(URL::fromString('http://test.com'));
    }

    public function testRearrangement() {
        $localCss = $this->dom->createElement('link');
        $localCss->setAttribute('rel', 'stylesheet');
        $localCss->setAttribute('href', 'http://test.com/local.css');
        $link = $this->dom->createElement('link');
        $remoteCss = $this->dom->createElement('link');
        $remoteCss->setAttribute('rel', 'stylesheet');
        $remoteCss->setAttribute('href', 'http://example.com');

        $this->head->appendChild($localCss);
        $this->head->appendChild($link);
        $this->head->appendChild($remoteCss);

        $this->body->appendChild($this->dom->createElement('div'));

        $this->filter->transformHTMLDOM($this->dom);

        $headLinks = $this->head->getElementsByTagName('link');
        $this->assertEquals(2, $headLinks->length);
        $this->assertSame($localCss, $headLinks[0]);
        $this->assertSame($link, $headLinks[1]);

        $bodyLinks = $this->body->getElementsByTagName('link');
        $this->assertEquals(1, $bodyLinks->length);
        $this->assertSame($remoteCss, $bodyLinks[0]);

    }

}
