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

        $securityToken = $this->createMock(ServiceSignature::class);
        $securityToken->method('sign')->willReturn('the-token');

        $this->filter = new CSSImagesOptimizationServiceHTMLFilter(
            $securityToken,
            URL::fromString('http://kibo-test.com/css/'),
            URL::fromString('http://kibo-test.com/images.php'),
            ['~' . preg_quote('http://kibo-test.com') . '~']
        );
    }

    /**
     * @dataProvider caseProvider
     */
    public function testRewritingInTags($input, $expected) {
        $style = $this->dom->createElement('style');
        $style->textContent = "body { $input }";
        $this->head->appendChild($style);
        $this->filter->transformHTMLDOM($this->dom);
        $this->assertEquals("body { $expected }", $style->textContent);
    }

    /**
     * @dataProvider caseProvider
     */
    public function testRewritingInAttributes($input, $expected) {
        $div = $this->dom->createElement('div');
        $div->setAttribute('style', $input);
        $this->body->appendChild($div);
        $this->filter->transformHTMLDOM($this->dom);
        $this->assertEquals($expected, $div->getAttribute('style'));
    }

    public function caseProvider() {
        return [
            [
                'background: url("images/image1")',
                'background: url("'
                . 'http://kibo-test.com/images.php?src='
                . urlencode('http://kibo-test.com/css/images/image1')
                . '&token=the-token'
                . '")'
            ],
            [
                "border-image: url('/images/image2')",
                'border-image: url(\''
                . 'http://kibo-test.com/images.php?src='
                . urlencode('http://kibo-test.com/images/image2')
                . '&token=the-token'
                . '\')'
            ]
        ];
    }

    public function testNotRewritingNonWhitelistedUrls() {
        $css = 'background: url("http://somewhere.else/img.png")';
        $style = $this->dom->createElement('style');
        $style->textContent = $css;
        $this->body->appendChild($style);
        $div = $this->dom->createElement('div');
        $div->setAttribute('style', $css);
        $this->body->appendChild($div);

        $this->filter->transformHTMLDOM($this->dom);

        $this->assertEquals($css, $style->textContent);
        $this->assertEquals($css, $div->getAttribute('style'));
    }

}
