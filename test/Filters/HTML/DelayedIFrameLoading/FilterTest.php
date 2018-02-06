<?php

namespace Kibo\Phast\Filters\HTML\DelayedIFrameLoading;

use Kibo\Phast\Filters\HTML\HTMLFilterTestCase;

class FilterTest extends HTMLFilterTestCase {

    public function testRewriting() {

        $regularFrame = $this->dom->createElement('iframe');
        $rewrittenFrame = $this->dom->createElement('iframe');
        $rewrittenFrame->setAttribute('src', 'http://kibo.test/index.php');
        $this->body->appendChild($regularFrame);
        $this->body->appendChild($rewrittenFrame);

        (new Filter())->transformHTMLDOM($this->dom);

        $this->assertFalse($regularFrame->hasAttribute('src'));
        $this->assertFalse($regularFrame->hasAttribute('data-phast-src'));
        $this->assertTrue($rewrittenFrame->hasAttribute('src'));
        $this->assertTrue($rewrittenFrame->hasAttribute('data-phast-src'));
        $this->assertEquals('about:blank', $rewrittenFrame->getAttribute('src'));
        $this->assertEquals('http://kibo.test/index.php', $rewrittenFrame->getAttribute('data-phast-src'));

        $scripts = $this->dom->getPhastJavaScripts();
        $this->assertCount(1, $scripts);
        $this->assertStringEndsWith('DelayedIFrameLoading/iframe-loader.js', $scripts[0]->getFilename());
    }

    public function testNotAppendingScript() {
        $frame = $this->dom->createElement('iframe');
        $this->body->appendChild($frame);
        (new Filter())->transformHTMLDOM($this->dom);
        $this->assertCount(0, $this->dom->getPhastJavaScripts());
    }

}
