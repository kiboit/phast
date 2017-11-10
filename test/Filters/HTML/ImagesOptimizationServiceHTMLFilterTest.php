<?php

namespace Kibo\Phast\Filters\HTML;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\ValueObjects\URL;

class ImagesOptimizationServiceHTMLFilterTest extends HTMLFilterTestCase {

    const SERVICE_URL = 'http://the-service.org/service.php';

    /**
     * @var ImagesOptimizationServiceHTMLFilter
     */
    private $filter;

    public function setUp() {
        parent::setUp();
        $this->filter = new ImagesOptimizationServiceHTMLFilter(
            new ServiceSignature($this->createMock(Cache::class)),
            URL::fromString('http://kibo-test.org'),
            URL::fromString(self::SERVICE_URL),
            ['~' . preg_quote('http://kibo-test.org') . '~']
        );
    }

    public function testImagesSrcRewriting() {
        $this->makeImage('/img?1');
        $this->makeImage('/img?2', 20);
        $this->makeImage('/img?3', null, 30);
        $this->makeImage('/img?4', 40, 50);

        $this->filter->transformHTMLDOM($this->dom);

        /** @var \DOMElement[] $images */
        $images = iterator_to_array($this->dom->getElementsByTagName('img'));

        $this->checkSrc($images[0]->getAttribute('src'), ['src' => 'http://kibo-test.org/img?1']);
        $this->checkSrc($images[1]->getAttribute('src'), ['src' => 'http://kibo-test.org/img?2', 'width' => 20]);
        $this->checkSrc($images[2]->getAttribute('src'), ['src' => 'http://kibo-test.org/img?3', 'height' => 30]);
        $this->checkSrc(
            $images[3]->getAttribute('src'),
            ['src' => 'http://kibo-test.org/img?4', 'width' => 40, 'height' => 50]
        );
    }

    public function testNoRewriteForImagesWithInlineSource() {
        $src = 'data:image/png;base64,iVBORw0KGgo';
        $img = $this->makeImage($src, 20, 30);
        $this->filter->transformHTMLDOM($this->dom);
        $this->assertEquals($src, $img->getAttribute('src'));
    }

    public function testNoRewriteForImagesWithNoSrcAttrSet() {
        $img = $this->makeImage('src', 20, 30);
        $img->removeAttribute('src');
        $this->filter->transformHTMLDOM($this->dom);
        $this->assertFalse($img->hasAttribute('src'));
    }

    public function testRewriteForSrcsetAttr() {
        $img = $this->makeImage('src', 20, 30);
        $img->setAttribute('srcset', '/val 2x, /val2, /val3 6w, data:blah 53');
        $this->filter->transformHTMLDOM($this->dom);

        $sets = array_map(function ($size) {
            return explode(' ', trim($size));
        }, explode(',', $img->getAttribute('srcset')));

        $this->assertCount(4, $sets);
        $this->checkSrc($sets[0][0], ['src' => 'http://kibo-test.org/val']);
        $this->assertEquals('2x', $sets[0][1]);
        $this->checkSrc($sets[1][0], ['src' => 'http://kibo-test.org/val2']);
        $this->assertCount(1, $sets[1]);
        $this->checkSrc($sets[2][0], ['src' => 'http://kibo-test.org/val3']);
        $this->assertEquals('6w', $sets[2][1]);
        $this->assertEquals('data:blah', $sets[3][0]);
        $this->assertEquals('53', $sets[3][1]);
    }

    public function testNotRewritingNonWhitelistHosts() {
        $img = $this->makeImage('http://external.place/img.png');
        $img->setAttribute('srcset', 'http://other.place/img.png, http://place3.place/img.png');
        $this->filter->transformHTMLDOM($this->dom);
        $this->assertEquals('http://external.place/img.png', $img->getAttribute('src'));
        $this->assertEquals('http://other.place/img.png, http://place3.place/img.png', $img->getAttribute('srcset'));
    }

    private function makeImage($src, $width = null, $height = null) {
        $img = $this->dom->createElement('img');
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

        $this->assertEquals('http', $components['scheme']);
        $this->assertEquals('the-service.org', $components['host']);
        $this->assertEquals('/service.php', $components['path']);

        $query = [];
        parse_str($components['query'], $query);
        $this->assertArrayHasKey('token', $query);
        $this->assertNotEmpty($query['token']);

        unset ($query['token']);

        $this->assertEquals($expectedParams, $query);
        
    }



}
