<?php

namespace Kibo\Phast\Filters\HTML\DelayedIFrameLoading;

use Kibo\Phast\Filters\HTML\HTMLFilterTestCase;

class FilterTest extends HTMLFilterTestCase {
    public function setUp() {
        parent::setUp();
        $this->filter = new Filter();
    }

    public function testRewriting() {
        $regularFrame = $this->makeMarkedElement('iframe');
        $rewrittenFrame = $this->makeMarkedElement('iframe');
        $rewrittenFrame->setAttribute('src', 'http://kibo.test/index.php');
        $this->body->appendChild($regularFrame);
        $this->body->appendChild($rewrittenFrame);

        $this->applyFilter();

        $regularFrame = $this->getMatchingElement($regularFrame);
        $rewrittenFrame = $this->getMatchingElement($rewrittenFrame);

        $this->assertFalse($regularFrame->hasAttribute('src'));
        $this->assertFalse($regularFrame->hasAttribute('data-phast-src'));
        $this->assertTrue($rewrittenFrame->hasAttribute('src'));
        $this->assertTrue($rewrittenFrame->hasAttribute('data-phast-src'));
        $this->assertEquals('about:blank', $rewrittenFrame->getAttribute('src'));
        $this->assertEquals('http://kibo.test/index.php', $rewrittenFrame->getAttribute('data-phast-src'));

        $this->assertHasCompiled('DelayedIFrameLoading/iframe-loader.js');
    }

    /** @dataProvider dontRewriteData */
    public function testDontRewrite($url) {
        $iframe = $this->insertIframeWithSrc($url);

        $this->applyFilter();

        $iframe = $this->getMatchingElement($iframe);
        $this->assertEquals($url, $iframe->getAttribute('src'));
        $this->assertFalse($iframe->hasAttribute('data-phast-src'));
    }

    private function insertIframeWithSrc($url) {
        $iframe = $this->makeMarkedElement('iframe');
        $iframe->setAttribute('src', $url);
        $this->body->appendChild($iframe);
        return $iframe;
    }

    public function testTrimSrc() {
        $input = '<iframe src=" http://nu.nl ">';

        $html = $this->applyFilter($input, true);

        $this->assertContains('"http://nu.nl"', $html);
    }

    public function dontRewriteData() {
        yield ['about:blank'];
        yield ['data:abc,def'];
    }

    public function testNotAppendingScript() {
        $frame = $this->dom->createElement('iframe');
        $this->body->appendChild($frame);
        $this->applyFilter();
        $this->assertHasNotCompiledScripts();
    }
}
