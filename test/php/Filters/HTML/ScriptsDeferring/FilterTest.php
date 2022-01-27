<?php

namespace Kibo\Phast\Filters\HTML\ScriptsDeferring;

use Kibo\Phast\Filters\HTML\HTMLFilterTestCase;

class FilterTest extends HTMLFilterTestCase {
    public function setUp(): void {
        parent::setUp();
        $this->filter = new Filter([]);
    }

    public function testRewriting() {
        $notInline  = $this->makeMarkedElement('script');
        $notInline->setAttribute('src', 'the-src');
        $notInline->setAttribute('defer', 'defer');
        $notInline->setAttribute('async', 'async');

        $inline = $this->makeMarkedElement('script');
        $inline->setAttribute('type', 'application/javascript');
        $inline->textContent = 'the-inline-content';

        $nonJS = $this->makeMarkedElement('script');
        $nonJS->setAttribute('type', 'non-js');

        $this->head->appendChild($notInline);
        $this->head->appendChild($inline);
        $this->head->appendChild($nonJS);

        $this->applyFilter();

        $notInline = $this->getMatchingElement($notInline);
        $inline = $this->getMatchingElement($inline);
        $nonJS = $this->getMatchingElement($nonJS);

        $this->assertEquals('text/phast', $notInline->getAttribute('type'));
        $this->assertEquals('the-src', $notInline->getAttribute('src'));
        $this->assertTrue($notInline->hasAttribute('defer'));
        $this->assertTrue($notInline->hasAttribute('async'));

        $this->assertEquals('text/phast', $inline->getAttribute('type'));
        $this->assertEquals('application/javascript', $inline->getAttribute('data-phast-original-type'));
        $this->assertFalse($inline->hasAttribute('async'));
        $this->assertEquals('the-inline-content', $inline->textContent);
        $this->assertFalse($inline->hasAttribute('data-phast-defer'));
        $this->assertFalse($inline->hasAttribute('data-phast-async'));

        $this->assertEquals('non-js', $nonJS->getAttribute('type'));

        $this->assertHasCompiled('ScriptsDeferring/rewrite.js');
    }

    public function testRewritingProxiedScript() {
        $script = $this->makeMarkedElement('script');
        $script->setAttribute('data-phast-params', 'abcd');
        $script->setAttribute('defer', 'defer');
        $script->setAttribute('async', 'async');
        $script->setAttribute('src', 'abcd');

        $this->body->appendChild($script);

        $this->applyFilter();

        $script = $this->getMatchingElement($script);
        $this->assertFalse($script->hasAttribute('async'));
        $this->assertFalse($script->hasAttribute('defer'));
        $this->assertFalse($script->hasAttribute('src'));
        $this->assertTrue($script->hasAttribute('data-phast-async'));
        $this->assertTrue($script->hasAttribute('data-phast-defer'));
    }

    public function testRewritingNonAsyncScript() {
        $script = $this->makeMarkedElement('script');

        $this->body->appendChild($script);

        $this->applyFilter();

        $script = $this->getMatchingElement($script);
        $this->assertFalse($script->hasAttribute('async'));
        $this->assertFalse($script->hasAttribute('defer'));
        $this->assertFalse($script->hasAttribute('data-phast-async'));
        $this->assertFalse($script->hasAttribute('data-phast-defer'));
    }

    public function testNoAsyncInlineScript() {
        $script = $this->makeMarkedElement('script');
        $script->setAttribute('async', '');

        $this->body->appendChild($script);

        $this->applyFilter();

        $script = $this->getMatchingElement($script);
        $this->assertFalse($script->hasAttribute('async'));
        $this->assertFalse($script->hasAttribute('defer'));
        $this->assertFalse($script->hasAttribute('data-phast-async'));
        $this->assertFalse($script->hasAttribute('data-phast-defer'));
    }

    public function disableRewritingData() {
        yield ['data-phast-no-defer', ''];
        yield ['data-pagespeed-no-defer', ''];
        yield ['data-cfasync', 'false'];
        yield ['data-cfasync', 'yolo', true];
        yield [null, null, false, "'phast-no-defer'"];
    }

    /** @dataProvider disableRewritingData */
    public function testDisableRewriting($attrName, $attrValue, $shouldDefer = false, $body = '') {
        $script = $this->makeMarkedElement('script');
        $script->setAttribute('type', 'text/javascript');
        $script->setAttribute('src', 'the-src');

        if ($attrName !== null) {
            $script->setAttribute($attrName, $attrValue);
        }

        $script->textContent = $body;

        $this->head->appendChild($script);

        $this->applyFilter();

        $script = $this->getMatchingElement($script);

        if ($shouldDefer) {
            $this->assertEquals('text/phast', $script->getAttribute('type'));
        } else {
            $this->assertEquals('text/javascript', $script->getAttribute('type'));
        }

        if ($attrName !== null) {
            $this->assertTrue($script->hasAttribute($attrName));
            $this->assertEquals($attrValue, $script->getAttribute($attrName));
        }
    }
}
