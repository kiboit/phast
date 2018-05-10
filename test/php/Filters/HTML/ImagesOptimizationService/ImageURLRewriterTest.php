<?php

namespace Kibo\Phast\Filters\HTML\ImagesOptimizationService;

use Kibo\Phast\Cache\Cache;
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
     * @var ImageInliningManager
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
        $this->inliningManager = new ImageInliningManager($this->createMock(Cache::class), 512);
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
            ],
            [
                "border-image: url(/images/image2), url(/images/image2)",
                sprintf(
                    'border-image: url(%1$s), url(%1$s)',
                    self::BASE_URL . '/images.php?src='
                    . urlencode(self::BASE_URL . '/images/image2')
                    . '&cacheMarker=123&token=the-token'
                )
            ]
        ];
    }

    public function testNotRewritingNonWhitelistedUrls() {
        $css = 'background: url("http://somewhere.else/img.png")';
        $actual = $this->getRewriter()->rewriteStyle($css);
        $this->assertEquals($css, $actual);
    }

    public function testNoRewriteForImagesWithInlineSource() {
        $src = 'data:image/png;base64,iVBORw0KGgo';
        $this->assertEquals($src, $this->getRewriter()->rewriteUrl($src));
    }

    /**
     * @param string $fileExtension
     * @param string $expectedMIMEType
     * @dataProvider inliningTypes
     */
    public function testInliningSmallImages($fileExtension, $expectedMIMEType) {
        $lastModificationTime = 123;
        $content = 'the-content-of-the-image-file';
        $this->retriever = $this->createMock(LocalRetriever::class);
        $this->retriever->method('getSize')
            ->willReturn(strlen($content));
        $this->retriever->method('retrieve')
            ->willReturn($content);
        $this->retriever->method('getCacheSalt')
            ->willReturn($lastModificationTime);

        $expectedDataUrl = "data:$expectedMIMEType;base64," . base64_encode($content);

        $rewriter = $this->getRewriter();
        $inputUrl1 = 'some-url.' . $fileExtension;
        $this->assertEquals($expectedDataUrl, $rewriter->rewriteUrl($inputUrl1));

        $inputUrlQuery = $inputUrl1 . '?query';
        $this->assertEquals($expectedDataUrl, $rewriter->rewriteUrl($inputUrlQuery));

        $inputUrlHash = $inputUrl1 . '#hash';
        $this->assertEquals($expectedDataUrl, $rewriter->rewriteUrl($inputUrlHash));

        $inlined = $rewriter->getInlinedResources();
        $this->assertCount(1, $inlined);
        $this->assertInstanceOf(Resource::class, $inlined[0]);
        $this->assertContains($inputUrl1, (string) $inlined[0]->getUrl());
        $this->assertEquals($lastModificationTime, $inlined[0]->getCacheSalt());

        $inputUrl2 = 'some-url-2.' . $fileExtension;
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
        $this->assertEquals($lastModificationTime, $inlined[0]->getCacheSalt());
        $this->assertEquals($lastModificationTime, $inlined[1]->getCacheSalt());

        $rewriter->rewriteUrl('http://somewhere.else.test/image');
        $this->assertCount(0, $rewriter->getInlinedResources());
    }

    public function inliningTypes() {
        return [
            ['gif', 'image/gif'],
            ['png', 'image/png'],
            ['jpg', 'image/jpeg'],
            ['jpeg', 'image/jpeg'],
            ['bmp', 'image/bmp'],
            ['webp', 'image/webp'],
            ['svg', 'image/svg+xml'],

            ['GIF', 'image/gif'],
            ['PNG', 'image/png'],
            ['JPG', 'image/jpeg'],
            ['JPEG', 'image/jpeg'],
            ['BMP', 'image/bmp'],
            ['WEBP', 'image/webp'],
            ['SVG', 'image/svg+xml']
        ];
    }

    public function testNotInliningWhenMIMETypeIsNotAnImage() {
        $image = 'some-very-dummy-image';
        $this->retriever = $this->createMock(LocalRetriever::class);
        $this->retriever->method('getSize')
            ->willReturn(strlen($image));
        $this->retriever->method('retrieve')
            ->willReturn($image);
        $url = $this->getRewriter()->rewriteUrl('some-url.ext');
        $this->assertStringStartsWith(self::BASE_URL, $url);
    }

    /**
     * @dataProvider cacheSaltData
     */
    public function testGetCacheSalt(array $params) {
        static $lastSalt, $called = false;

        $this->securityToken->method('getCacheSalt')
            ->willReturn($params['token']);
        $inliner = new ImageURLRewriter(
            $this->securityToken,
            $this->retriever,
            new ImageInliningManager($this->createMock(Cache::class), $params['maxImageSize']),
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
            'maxImageSize' => 512
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
