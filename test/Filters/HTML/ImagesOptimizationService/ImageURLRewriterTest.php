<?php

namespace Kibo\Phast\Filters\HTML\ImagesOptimizationService;

use Kibo\Phast\PhastTestCase;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\Services\ServiceRequest;
use Kibo\Phast\ValueObjects\URL;

class ImageURLRewriterTest extends PhastTestCase {

    /**
     * @var ImageURLRewriter
     */
    private $rewriter;

    public function setUp($rewriteFormat = null) {
        parent::setUp();

        $securityToken = $this->createMock(ServiceSignature::class);
        $securityToken->method('sign')->willReturn('the-token');
        $retriever = $this->createMock(Retriever::class);
        $retriever->method('getLastModificationTime')->willReturn(false);
        $this->rewriter = new ImageURLRewriter(
            $securityToken,
            $retriever,
            URL::fromString(self::BASE_URL . '/css/'),
            URL::fromString(self::BASE_URL . '/images.php'),
            ['~' . preg_quote(self::BASE_URL . '') . '~'],
            $rewriteFormat
        );
    }

    /**
     * @param $input
     * @param $expected
     * @dataProvider caseProvider
     */
    public function testRewritingImageURLsInStyles($input, $expected) {
        $actual = $this->rewriter->rewriteStyle($input);
        $this->assertEquals($expected, $actual);
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
                'background: url(" images/image1 ")',
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
        $actual = $this->rewriter->rewriteStyle($css);
        $this->assertEquals($css, $actual);
    }

    public function testUsingCorrectRewriteFormat() {
        $queryFormat = $this->rewriter->rewriteUrl('/img');
        $this->setUp(ServiceRequest::FORMAT_PATH);
        $pathFormat = $this->rewriter->rewriteUrl('/img');
        $this->assertNotEquals($queryFormat, $pathFormat);
    }

    public function testNoRewriteForImagesWithInlineSource() {
        $src = 'data:image/png;base64,iVBORw0KGgo';
        $this->assertEquals($src, $this->rewriter->rewriteUrl($src));
    }
}
