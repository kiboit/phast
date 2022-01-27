<?php

namespace Kibo\Phast\Filters\HTML\ImagesOptimizationService\Tags;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\Filters\HTML\HTMLFilterTestCase;
use Kibo\Phast\Filters\HTML\ImagesOptimizationService\ImageInliningManager;
use Kibo\Phast\Filters\HTML\ImagesOptimizationService\ImageURLRewriter;
use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\Services\ServiceRequest;
use Kibo\Phast\ValueObjects\URL;

class TagsFilterTest extends HTMLFilterTestCase {
    const SERVICE_URL = 'http://the-service.org/service.php';

    private $files;

    public function setUp(): void {
        parent::setUp();

        ServiceRequest::setDefaultSerializationMode(ServiceRequest::FORMAT_QUERY);

        $signature = $this->createMock(ServiceSignature::class);
        $signature->method('sign')
            ->willReturn('the-token');

        $this->files = null;
        $retriever = $this->createMock(LocalRetriever::class);
        $retriever->method('getCacheSalt')->will($this->returnCallback(function ($file) {
            if ($this->files === null || in_array($file, $this->files)) {
                return 12345678;
            }
            return false;
        }));
        $retriever->method('getSize')->will($this->returnCallback(function ($file) {
            if ($this->files === null || in_array($file, $this->files)) {
                return 100000;
            }
            return false;
        }));

        $this->filter = new Filter(new ImageURLRewriter(
            $signature,
            $retriever,
            new ImageInliningManager($this->createMock(Cache::class), 512),
            URL::fromString(self::BASE_URL),
            URL::fromString(self::SERVICE_URL),
            ['~' . preg_quote(self::BASE_URL) . '~']
        ));
    }

    public function testImagesSrcRewriting() {
        $this->makeImage('/img?1');
        $this->makeImage('/img?2', 20);
        $this->makeImage('/img?3', null, 30);
        $this->makeImage('/img?4', 40, 50);

        $this->applyFilter();

        /** @var \DOMElement[] $images */
        $images = iterator_to_array($this->dom->getElementsByTagName('img'));

        $this->checkSrc($images[0]->getAttribute('src'), ['src' => self::BASE_URL . '/img?1']);
        $this->checkSrc($images[1]->getAttribute('src'), ['src' => self::BASE_URL . '/img?2']);
        $this->checkSrc($images[2]->getAttribute('src'), ['src' => self::BASE_URL . '/img?3']);
        $this->checkSrc(
            $images[3]->getAttribute('src'),
            ['src' => self::BASE_URL . '/img?4']
        );
    }

    public function testLazySrcRewriting() {
        $img = $this->makeMarkedElement('img');
        $img->setAttribute('data-lazy-src', '/img?1');
        $img->setAttribute('data-lazy-srcset', '/img?2 2x');
        $this->body->appendChild($img);

        $this->applyFilter();

        /** @var \DOMElement[] $images */
        $images = iterator_to_array($this->dom->getElementsByTagName('img'));

        $this->checkSrc($images[0]->getAttribute('data-lazy-src'), ['src' => self::BASE_URL . '/img?1']);
        $this->checkSrc(preg_replace('/\s.*$/', '', $images[0]->getAttribute('data-lazy-srcset')), ['src' => self::BASE_URL . '/img?2']);
    }

    public function testPictureSourceRewriting() {
        $html = '<html><body>';
        $html .= '<picture>';
        $html .= '<source srcset="/img1">';
        $html .= '<source src="/img2">';
        $html .= '<a src="/img3">something</a>';
        $html .= '<source src="/img4" srcset="/img5">';
        $html .= '<img src="/img6">';
        $html .= '</picture>';
        $html .= '<video>';
        $html .= '<source srcset="/img7">';
        $html .= '</video>';
        $html .= '</body></html>';

        $filtered = $this->applyFilter($html, true);
        $regexp = '~' . preg_replace('~/(img|ignore)\d~', '([^"]*)', $html) . '~';

        $matches = [];
        if (!preg_match($regexp, $filtered, $matches)) {
            $this->fail('Could not match filtered html!');
        }
        foreach ([1, 2, 4, 5, 6] as $i) {
            $this->checkSrc(str_replace('&amp;', '&', $matches[$i]), ['src' => self::BASE_URL . '/img' . $i]);
        }
        $this->assertEquals('/img3', $matches[3]);
        $this->assertEquals('/img7', $matches[7]);
    }

    /** @dataProvider dontRewriteMediaSourceData */
    public function testDontRewriteMediaSource($tag) {
        $html = "<picture><$tag><source src=\"/canary\">";
        $filtered = $this->applyFilter($html, true);
        $this->assertStringContainsString('"/canary"', $filtered);
    }

    public function dontRewriteMediaSourceData() {
        yield ['video'];
        yield ['audio'];
    }

    public function testRewriteSrcWithSpace() {
        $html = '<html><body><img src=" /img "></body></html>';
        $this->applyFilter($html);
        $this->checkSrc(
            $this->dom->getElementsByTagName('img')->item(0)->getAttribute('src'),
            ['src' => self::BASE_URL . '/img']
        );
    }

    public function testNoRewriteForImagesWithNoSrcAttrSet() {
        $img = $this->makeImage('src', 20, 30);
        $img->removeAttribute('src');
        $this->applyFilter();
        $this->getMatchingElement($img);
        $this->assertFalse($img->hasAttribute('src'));
    }

    public function testRewriteForSrcsetAttr() {
        $img = $this->makeImage('src', 20, 30);
        $img->setAttribute('srcset', '/val 2x, /val2, /val3 6w, data:blah 53');
        $this->applyFilter();

        $img = $this->getMatchingElement($img);
        $sets = array_map(function ($size) {
            return explode(' ', trim($size));
        }, explode(',', $img->getAttribute('srcset')));

        $this->assertCount(4, $sets);
        $this->checkSrc($sets[0][0], ['src' => self::BASE_URL . '/val']);
        $this->assertEquals('2x', $sets[0][1]);
        $this->checkSrc($sets[1][0], ['src' => self::BASE_URL . '/val2']);
        $this->assertCount(1, $sets[1]);
        $this->checkSrc($sets[2][0], ['src' => self::BASE_URL . '/val3']);
        $this->assertEquals('6w', $sets[2][1]);
        $this->assertEquals('data:blah', $sets[3][0]);
        $this->assertEquals('53', $sets[3][1]);
    }

    public function testRespectingBaseTagInSrc() {
        $this->addBaseTag('/new-root/');
        $img = $this->makeImage('the-image.jpg');
        $this->applyFilter();
        $img = $this->getMatchingElement($img);
        $this->checkSrc($img->getAttribute('src'), ['src' => self::BASE_URL . '/new-root/the-image.jpg']);
    }

    public function testRespectingBaseTagInSrcset() {
        $this->addBaseTag('/new-root/');
        $img = $this->makeImage('the-image.jpg');
        $img->setAttribute('srcset', 'the-image.jpg');
        $this->applyFilter();
        $img = $this->getMatchingElement($img);
        $this->checkSrc($img->getAttribute('srcset'), ['src' => self::BASE_URL . '/new-root/the-image.jpg']);
    }

    public function testDataAttributesRewriting() {
        $this->files = [self::BASE_URL . '/image.jpg'];

        $html =
            '<html><body><div data-a="/image.jpg" data-b="/404.jpg" data-c="/text.txt"></div></body></html>';

        $this->applyFilter($html);

        $div = $this->dom->getElementsByTagName('div')->item(0);
        $this->checkSrc($div->getAttribute('data-a'), ['src' => self::BASE_URL . '/image.jpg']);
        $this->assertEquals('/404.jpg', $div->getAttribute('data-b'));
        $this->assertEquals('/text.txt', $div->getAttribute('data-c'));
    }

    public function testNoRewritingInHead() {
        $html = '<html><head><link href="/image.jpg"></head></html>';

        $this->applyFilter($html);

        $meta = $this->dom->getElementsByTagName('link')->item(0);
        $this->assertEquals('/image.jpg', $meta->getAttribute('href'));
    }

    public function testNoRewritingMeta() {
        $html = '<html><body><meta href="/image.jpg"></body></html>';

        $this->applyFilter($html);

        $meta = $this->dom->getElementsByTagName('meta')->item(0);
        $this->assertEquals('/image.jpg', $meta->getAttribute('href'));
    }

    public function testRewriteAMPImage() {
        $html = '<html><body><amp-img src="/image"></body></html>';

        $this->applyFilter($html);

        $img = $this->dom->getElementsByTagName('amp-img')->item(0);
        $this->assertStringContainsString('service.php', $img->getAttribute('src'));
    }

    public function testRevolutionSliderFix() {
        $html = '<html><body>' .
            '<img src="/images/transparent.png" class="rev-slidebg">' .
            '<img src="/images/transparent.png">' .
            '</body></html>';

        $this->applyFilter($html);

        $img = $this->dom->getElementsByTagName('img')->item(0);
        $this->assertStringNotContainsString('service.php', $img->getAttribute('src'));

        $img = $this->dom->getElementsByTagName('img')->item(1);
        $this->assertStringContainsString('service.php', $img->getAttribute('src'));
    }

    private function makeImage($src, $width = null, $height = null) {
        $img = $this->makeMarkedElement('img');
        $img->setAttribute('src', $src);
        if (!is_null($width)) {
            $img->setAttribute('width', $width);
        }
        if (!is_null($height)) {
            $img->setAttribute('height', $height);
        }
        $this->body->appendChild($img);
        return $img;
    }

    private function checkSrc($url, $expectedParams) {
        $components = parse_url($url);

        $this->assertArrayHasKey('scheme', $components);
        $this->assertArrayHasKey('host', $components);
        $this->assertArrayHasKey('path', $components);
        $this->assertArrayHasKey('query', $components);

        $this->assertEquals('http', $components['scheme']);
        $this->assertEquals('the-service.org', $components['host']);
        $this->assertEquals('/service.php', $components['path']);

        $query = [];
        parse_str($components['query'], $query);
        $this->assertArrayHasKey('token', $query);
        $this->assertNotEmpty($query['token']);

        $this->assertEquals(12345678, $query['cacheMarker']);

        unset($query['token']);
        unset($query['cacheMarker']);

        $this->assertEquals($expectedParams, $query);
    }
}
