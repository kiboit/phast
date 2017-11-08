<?php

namespace Kibo\Phast\Filters\HTML;

use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\ValueObjects\URL;

class CSSImagesOptimizationServiceHTMLFilterTest extends HTMLFilterTestCase {

    public function testRewriting() {
        $style1 = $this->dom->createElement('style');
        $style1->textContent = 'body { background: url("images/image1") }';
        $style2 = $this->dom->createElement('style');
        $style2->textContent = "body { border-image: url('/images/image2') }";
        $this->head->appendChild($style1);
        $this->body->appendChild($style2);

        $expected1 = 'http://kibo-test.com/images.php?src='
                    . urlencode('http://kibo-test.com/css/images/image1')
                    . '&token=the-token';
        $expected2 = 'http://kibo-test.com/images.php?src='
            . urlencode('http://kibo-test.com/images/image2')
            . '&token=the-token';

        $securityToken = $this->createMock(ServiceSignature::class);
        $securityToken->expects($this->exactly(2))
            ->method('sign')
            ->willReturn('the-token');

        $filter = new CSSImagesOptimizationServiceHTMLFilter(
            $securityToken,
            URL::fromString('http://kibo-test.com/css/'),
            URL::fromString('http://kibo-test.com/images.php')
        );
        $filter->transformHTMLDOM($this->dom);
        $this->assertEquals("body { background: url(\"$expected1\") }", $style1->textContent);
        $this->assertEquals("body { border-image: url('$expected2') }", $style2->textContent);

    }

}
