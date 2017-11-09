<?php

namespace Kibo\Phast\Filters\HTML;

class ScriptsDeferHTMLFilterTest extends HTMLFilterTestCase {

    /**
     * @var ScriptsDeferHTMLFilter
     */
    private $filter;

    public function setUp() {
        parent::setUp();
        $this->filter = new ScriptsDeferHTMLFilter();
    }

    public function testRewriting() {
        $notInline  = $this->dom->createElement('script');
        $notInline->setAttribute('src', 'the-src');
        $notInline->setAttribute('defer', 'defer');
        $notInline->setAttribute('async', 'async');

        $inline = $this->dom->createElement('script');
        $inline->setAttribute('type', 'application/javascript');
        $inline->textContent = 'the-inline-content';

        $nonJS = $this->dom->createElement('script');
        $nonJS->setAttribute('type', 'non-js');

        $this->head->appendChild($notInline);
        $this->head->appendChild($inline);
        $this->head->appendChild($nonJS);

        $this->filter->transformHTMLDOM($this->dom);

        $elements = $this->head->childNodes;
        $this->assertEquals(3, $elements->length);

        $this->assertEquals('phast-script', $notInline->getAttribute('type'));
        $this->assertEquals('the-src', $notInline->getAttribute('src'));
        $this->assertTrue($notInline->hasAttribute('defer'));
        $this->assertTrue($notInline->hasAttribute('async'));

        $this->assertEquals('phast-script', $inline->getAttribute('type'));
        $this->assertEquals('application/javascript', $inline->getAttribute('data-phast-original-type'));
        $this->assertFalse($inline->hasAttribute('async'));
        $this->assertEquals('the-inline-content', $inline->textContent);

        $this->assertEquals('non-js', $nonJS->getAttribute('type'));

        $this->assertEquals(1, $this->body->childNodes->length);
        $this->assertEquals('script', $this->body->childNodes->item(0)->tagName);
    }

    public function testDisableRewriting() {
        $script = $this->dom->createElement('script');
        $script->setAttribute('type', 'text/javascript');
        $script->setAttribute('src', 'the-src');
        $script->setAttribute('data-phast-no-defer', '');

        $this->head->appendChild($script);

        $this->filter->transformHTMLDOM($this->dom);

        $elements = $this->head->childNodes;
        $this->assertEquals(1, $elements->length);

        $this->assertEquals('text/javascript', $elements->item(0)->getAttribute('type'));
    }

}
