<?php

namespace Kibo\Phast\Filters\HTML\ScriptsDeferring;

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

        $this->assertEquals('phast-script', $notInline->getAttribute('type'));
        $this->assertEquals('the-src', $notInline->getAttribute('src'));
        $this->assertTrue($notInline->hasAttribute('defer'));
        $this->assertTrue($notInline->hasAttribute('async'));

        $this->assertEquals('phast-script', $inline->getAttribute('type'));
        $this->assertEquals('application/javascript', $inline->getAttribute('data-phast-original-type'));
        $this->assertFalse($inline->hasAttribute('async'));
        $this->assertEquals('the-inline-content', $inline->textContent);

        $this->assertEquals('non-js', $nonJS->getAttribute('type'));

        $scripts = $this->dom->getPhastJavaScripts();
        $this->assertCount(1, $scripts);
        $this->assertStringEndsWith('ScriptsDeferring/rewrite.js', $scripts[0]->getFilename());
    }

    public function testDisableRewriting() {
        $script = $this->dom->createElement('script');
        $script->setAttribute('type', 'text/javascript');
        $script->setAttribute('src', 'the-src');
        $script->setAttribute('data-phast-no-defer', '');

        $this->head->appendChild($script);

        $this->filter->transformHTMLDOM($this->dom);

        $this->assertEquals('text/javascript', $script->getAttribute('type'));
        $this->assertFalse($script->hasAttribute('data-phast-no-defer'));
    }

}
