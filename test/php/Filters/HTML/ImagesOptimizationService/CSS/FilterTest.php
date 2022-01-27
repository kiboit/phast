<?php

namespace Kibo\Phast\Filters\HTML\ImagesOptimizationService\CSS;

use Kibo\Phast\Filters\HTML\HTMLFilterTestCase;
use Kibo\Phast\Filters\HTML\ImagesOptimizationService\ImageURLRewriter;

class FilterTest extends HTMLFilterTestCase {
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $rewriterMock;

    public function setUp(): void {
        parent::setUp();
        $this->rewriterMock = $this->createMock(ImageURLRewriter::class);
        $this->rewriterMock->method('rewriteStyle')
            ->willReturn('rewritten-style');
        $this->filter = new Filter($this->rewriterMock);
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
