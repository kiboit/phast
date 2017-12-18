<?php

namespace Kibo\Phast\Filters\HTML\ImagesOptimizationService\CSS;

use Kibo\Phast\Filters\HTML\HTMLFilterTestCase;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\ValueObjects\URL;

class FilterTest extends HTMLFilterTestCase {

    /**
     * @var Filter
     */
    private $filter;

    public function setUp() {
        parent::setUp();

        $securityToken = $this->createMock(ServiceSignature::class);
        $securityToken->method('sign')->willReturn('the-token');
        $retriever = $this->createMock(Retriever::class);
        $retriever->method('getLastModificationTime')->willReturn(false);

        $this->filter = new Filter(
            $securityToken,
            $retriever,
            URL::fromString(self::BASE_URL . '/css/'),
            URL::fromString(self::BASE_URL . '/images.php'),
            ['~' . preg_quote(self::BASE_URL . '') . '~']
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
                . self::BASE_URL . '/images.php?src='
                . urlencode(self::BASE_URL . '/css/images/image1')
                . '&token=the-token'
                . '")'
            ],
            [
                "border-image: url('/images/image2')",
                'border-image: url(\''
                . self::BASE_URL . '/images.php?src='
                . urlencode(self::BASE_URL . '/images/image2')
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
