<?php

namespace Kibo\Phast\Filters\HTML\ImagesOptimizationService\CSS;

use Kibo\Phast\Filters\HTML\HTMLFilterTestCase;
use Kibo\Phast\Filters\HTML\ImagesOptimizationService\ImageURLRewriter;

class FilterTest extends HTMLFilterTestCase {

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $rewriterMock;

    public function setUp() {
        parent::setUp();
        $this->rewriterMock = $this->createMock(ImageURLRewriter::class);
        $this->rewriterMock->method('rewriteStyle')
            ->willReturn('rewritten-style');
        $this->filter = new Filter($this->rewriterMock);
    }

    public function testRewritingInTags() {
        $style = $this->makeMarkedElement('style');
        $style->textContent = "the-original-style";
        $this->head->appendChild($style);

        $this->applyFilter();

        $style = $this->getMatchingElement($style);
        $this->assertEquals("rewritten-style", $style->textContent);
    }

    public function testRewritingInAttributes() {
        $div = $this->makeMarkedElement('div');
        $div->setAttribute('style', 'the-original-style');
        $this->body->appendChild($div);

        $this->applyFilter();

        $div = $this->getMatchingElement($div);
        $this->assertEquals('rewritten-style', $div->getAttribute('style'));
    }

}
