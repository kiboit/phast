<?php

namespace Kibo\Phast\Filters\HTML\ImagesOptimizationService;

use Kibo\Phast\PhastTestCase;
use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\Services\ServiceRequest;
use Kibo\Phast\ValueObjects\Resource;
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
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $inliningManager;

    public function setUp($rewriteFormat = null) {
        parent::setUp();

        ServiceRequest::setDefaultSerializationMode(ServiceRequest::FORMAT_QUERY);

        $this->securityToken = $this->createMock(ServiceSignature::class);
        $this->securityToken->method('sign')->willReturn('the-token');
        $this->retriever = $this->createMock(LocalRetriever::class);
        $this->retriever->method('getCacheSalt')->willReturn(123);
        $this->retriever->method('getSize')->willReturn(1024);
        $this->inliningManager = $this->createMock(ImageInliningManager::class);
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
                . '")',
            ],
            [
                'background: url(" images/image1 ")',
                'background: url("'
                . self::BASE_URL . '/images.php?src='
                . urlencode(self::BASE_URL . '/css/images/image1')
                . '&cacheMarker=123&token=the-token'
                . '")',
            ],
            [
                "border-image: url('/images/image2')",
                'border-image: url(\''
                . self::BASE_URL . '/images.php?src='
                . urlencode(self::BASE_URL . '/images/image2')
                . '&cacheMarker=123&token=the-token'
                . '\')',
            ],
            [
                'border-image: url(/images/image2), url(/images/image2)',
                sprintf(
                    'border-image: url(%1$s), url(%1$s)',
                    self::BASE_URL . '/images.php?src='
                    . urlencode(self::BASE_URL . '/images/image2')
                    . '&cacheMarker=123&token=the-token'
                ),
            ],
        ];
    }

    /** @dataProvider dontRewriteData */
    public function testDontRewrite($css) {
        $actual = $this->getRewriter()->rewriteStyle($css);
        $this->assertEquals($css, $actual);
    }

    public function dontRewriteData() {
        yield ['background: url("http://somewhere.else/img.png")'];
        yield ['background: url(#yolo)'];
    }

    public function testNoRewriteForImagesWithInlineSource() {
        $src = 'data:image/png;base64,iVBORw0KGgo';
        $this->assertEquals($src, $this->getRewriter()->rewriteUrl($src));
    }

    public function testInliningSmallImages() {
        $expectedDataUrl = 'rewritten-url';

        $this->inliningManager->method('getUrlForInlining')
            ->willReturn($expectedDataUrl);


        $rewriter = $this->getRewriter();
        $inputUrl1 = 'some-url.jpg';
        $this->assertEquals($expectedDataUrl, $rewriter->rewriteUrl($inputUrl1));


        $inlined = $rewriter->getInlinedResources();
        $this->assertCount(1, $inlined);
        $this->assertInstanceOf(Resource::class, $inlined[0]);
        $this->assertContains($inputUrl1, (string) $inlined[0]->getUrl());

        $inputUrl2 = 'some-url-2.jpg';
        $css = "background: url(\"$inputUrl2\"); background: url(\"$inputUrl1\"); background: url(\"$inputUrl2\");";
        $expectedCSS = str_replace([$inputUrl2, $inputUrl1], $expectedDataUrl, $css);
        $rewrittenCSS = $rewriter->rewriteStyle($css);
        $this->assertEquals($rewrittenCSS, $expectedCSS);

        $inlined = $rewriter->getInlinedResources();
        $this->assertCount(2, $inlined);
        $this->assertInstanceOf(Resource::class, $inlined[0]);
        $this->assertInstanceOf(Resource::class, $inlined[1]);
        $this->assertContains($inputUrl2, (string) $inlined[0]->getUrl());
        $this->assertContains($inputUrl1, (string) $inlined[1]->getUrl());
    }

    public function testNotInliningWhenManagerReturnsNull() {
        $rewriter = $this->getRewriter();
        $url = $rewriter->rewriteUrl('some-url.jpg');
        $this->assertStringStartsWith(self::BASE_URL, $url);
        $this->assertCount(0, $rewriter->getInlinedResources());
    }

    /**
     * @dataProvider cacheSaltData
     */
    public function testGetCacheSalt(array $params) {
        static $lastSalt, $called = false;

        $this->inliningManager->expects($this->once())
            ->method('getMaxImageInliningSize')
            ->willReturn($params['maxImageSize']);

        $this->securityToken->method('getCacheSalt')
            ->willReturn($params['token']);
        $inliner = new ImageURLRewriter(
            $this->securityToken,
            $this->retriever,
            $this->inliningManager,
            URL::fromString($params['baseUrl']),
            URL::fromString($params['serviceUrl']),
            $params['whitelist']
        );
        $salt = $inliner->getCacheSalt();
        if ($called) {
            $this->assertNotEquals($lastSalt, $salt);
        }
        $called = true;
        $lastSalt = $salt;
    }

    public function cacheSaltData() {
        $params = [
            'token' => 'the-token',
            'baseUrl' => self::BASE_URL . '/base-url',
            'serviceUrl' => self::BASE_URL . '/service-url',
            'whitelist' => ['key' => 'val'],
            'maxImageSize' => 512,
        ];
        yield [$params];
        foreach ($params as $key => $val) {
            $new = $params;
            if ($key == 'whitelist') {
                $new[$key] = ['key1' => 'val1'];
            } else {
                $new[$key] .= '1';
            }
            yield [$new];
            yield [$params];
        }
    }

    /**
     * @param int|null $rewriteFormat
     * @return ImageURLRewriter
     */
    private function getRewriter($rewriteFormat = null) {
        return new ImageURLRewriter(
            $this->securityToken,
            $this->retriever,
            $this->inliningManager,
            URL::fromString(self::BASE_URL . '/css/'),
            URL::fromString(self::BASE_URL . '/images.php'),
            ['~' . preg_quote(self::BASE_URL . '') . '~']
        );
    }
}
