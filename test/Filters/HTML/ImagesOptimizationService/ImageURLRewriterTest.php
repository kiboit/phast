<?php

namespace Kibo\Phast\Filters\HTML\ImagesOptimizationService;

use Kibo\Phast\PhastTestCase;
use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\Services\ServiceRequest;
use Kibo\Phast\ValueObjects\URL;

class ImageURLRewriterTest extends PhastTestCase {

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $securityToken;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $retriever;

    /**
     * @var ImageURLRewriter
     */
    private $rewriter;

    public function setUp($rewriteFormat = null) {
        parent::setUp();

        $this->securityToken = $this->createMock(ServiceSignature::class);
        $this->securityToken->method('sign')->willReturn('the-token');
        $this->retriever = $this->createMock(LocalRetriever::class);
        $this->retriever->method('getLastModificationTime')->willReturn(123);
        $this->retriever->method('getSize')->willReturn(1024);
    }

    /**
     * @param $input
     * @param $expected
     * @dataProvider caseProvider
     */
    public function testRewritingImageURLsInStyles($input, $expected) {
        $actual = $this->getRewriter()->rewriteStyle($input);
        $this->assertEquals($expected, $actual);
    }

    public function caseProvider() {
        return [
            [
                'background: url("images/image1")',
                'background: url("'
                . self::BASE_URL . '/images.php?src='
                . urlencode(self::BASE_URL . '/css/images/image1')
                . '&cacheMarker=123&token=the-token'
                . '")'
            ],
            [
                'background: url(" images/image1 ")',
                'background: url("'
                . self::BASE_URL . '/images.php?src='
                . urlencode(self::BASE_URL . '/css/images/image1')
                . '&cacheMarker=123&token=the-token'
                . '")'
            ],
            [
                "border-image: url('/images/image2')",
                'border-image: url(\''
                . self::BASE_URL . '/images.php?src='
                . urlencode(self::BASE_URL . '/images/image2')
                . '&cacheMarker=123&token=the-token'
                . '\')'
            ]
        ];
    }

    public function testNotRewritingNonWhitelistedUrls() {
        $css = 'background: url("http://somewhere.else/img.png")';
        $actual = $this->getRewriter()->rewriteStyle($css);
        $this->assertEquals($css, $actual);
    }

    public function testUsingCorrectRewriteFormat() {
        $queryFormat = $this->getRewriter()->rewriteUrl('/img');
        $pathFormat = $this->getRewriter(ServiceRequest::FORMAT_PATH)->rewriteUrl('/img');
        $this->assertNotEquals($queryFormat, $pathFormat);
    }

    public function testNoRewriteForImagesWithInlineSource() {
        $src = 'data:image/png;base64,iVBORw0KGgo';
        $this->assertEquals($src, $this->getRewriter()->rewriteUrl($src));
    }

    public function testInliningSmallImages() {
        $svg = '<?xml version="1.0"><svg focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"></path></svg>';
        $this->retriever = $this->createMock(LocalRetriever::class);
        $this->retriever->method('getSize')
            ->willReturn(strlen($svg));
        $this->retriever->method('retrieve')
            ->willReturn($svg);

        $url = $this->getRewriter()->rewriteUrl('some-url');
        $this->assertEquals('data:image/svg+xml;base64,' . base64_encode($svg), $url);
    }

    public function testNotInliningWhenMIMETypeIsNotAnImage() {
        $image = 'some-very-dummy-image';
        $this->retriever = $this->createMock(LocalRetriever::class);
        $this->retriever->method('getSize')
            ->willReturn(strlen($image));
        $this->retriever->method('retrieve')
            ->willReturn($image);
        $url = $this->getRewriter()->rewriteUrl('some-url');
        $this->assertStringStartsWith(self::BASE_URL, $url);
    }


    /**
     * @param int|null $rewriteFormat
     * @return ImageURLRewriter
     */
    private function getRewriter($rewriteFormat = null) {
        return new ImageURLRewriter(
            $this->securityToken,
            $this->retriever,
            URL::fromString(self::BASE_URL . '/css/'),
            URL::fromString(self::BASE_URL . '/images.php'),
            ['~' . preg_quote(self::BASE_URL . '') . '~'],
            $rewriteFormat,
            512
        );
    }
}
