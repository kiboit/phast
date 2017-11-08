<?php

namespace Kibo\Phast\Filters\HTML;

use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\ValueObjects\URL;

class CSSImagesOptimizationServiceHTMLFilterTest extends HTMLFilterTestCase {

    /**
     * @var CSSImagesOptimizationServiceHTMLFilter
     */
    private $filter;

    private $styles;

    private $expected;

    public function setUp() {
        parent::setUp();

        $this->styles = [
            'background: url("images/image1")',
            "border-image: url('/images/image2')"
        ];

        $this->expected = [
            'background: url("'
            . 'http://kibo-test.com/images.php?src='
            . urlencode('http://kibo-test.com/css/images/image1')
            . '&token=the-token'
            . '")',

            'border-image: url(\''
            . 'http://kibo-test.com/images.php?src='
            . urlencode('http://kibo-test.com/images/image2')
            . '&token=the-token'
            . '\')'
        ];

        $securityToken = $this->createMock(ServiceSignature::class);
        $securityToken->expects($this->exactly(2))
                      ->method('sign')
                      ->willReturn('the-token');

        $this->filter = new CSSImagesOptimizationServiceHTMLFilter(
            $securityToken,
            URL::fromString('http://kibo-test.com/css/'),
            URL::fromString('http://kibo-test.com/images.php')
        );
    }

    public function testRewritingInTags() {
        $style1 = $this->dom->createElement('style');
        $style1->textContent = "body { {$this->styles[0]} }";
        $style2 = $this->dom->createElement('style');
        $style2->textContent = "body { {$this->styles[1]} }";
        $this->head->appendChild($style1);
        $this->body->appendChild($style2);

        $this->filter->transformHTMLDOM($this->dom);
        $this->assertEquals("body { {$this->expected[0]} }", $style1->textContent);
        $this->assertEquals("body { {$this->expected[1]} }", $style2->textContent);
    }

    public function testRewritingInAttributes() {
        $div1 = $this->dom->createElement('div');
        $div1->setAttribute('style', $this->styles[0]);
        $this->body->appendChild($div1);
        $div2 = $this->dom->createElement('div');
        $div2->setAttribute('style', $this->styles[1]);
        $this->body->appendChild($div2);

        $this->filter->transformHTMLDOM($this->dom);

        $this->assertEquals($this->expected[0], $div1->getAttribute('style'));
        $this->assertEquals($this->expected[1], $div2->getAttribute('style'));
    }

}
