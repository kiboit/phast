<?php

namespace Kibo\Phast\Filters;

use Kibo\Phast\Security\ImagesOptimizationSignature;

class ImagesOptimizationServiceHTMLFilterTest extends HTMLFilterTestCase {

    const SERVICE_URL = 'http://the-service.org/service.php';

    /**
     * @var ImagesOptimizationServiceHTMLFilter
     */
    private $filter;

    public function setUp() {
        parent::setUp();
        $this->filter = new ImagesOptimizationServiceHTMLFilter(
            new ImagesOptimizationSignature('the-token'),
            self::BASE_URL,
            self::SERVICE_URL
        );
    }

    public function testImagesSrcRewriting() {
        $this->makeImage('img?1');
        $this->makeImage('img?2', 20);
        $this->makeImage('img?3', null, 30);
        $this->makeImage('img?4', 40, 50);

        $this->filter->transformHTMLDOM($this->dom);

        /** @var \DOMElement[] $images */
        $images = iterator_to_array($this->dom->getElementsByTagName('img'));

        $this->checkSrc($images[0]->getAttribute('src'), ['src' => 'img?1']);
        $this->checkSrc($images[1]->getAttribute('src'), ['src' => 'img?2', 'width' => 20]);
        $this->checkSrc($images[2]->getAttribute('src'), ['src' => 'img?3', 'height' => 30]);
        $this->checkSrc($images[3]->getAttribute('src'), ['src' => 'img?4', 'width' => 40, 'height' => 50]);
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

    public function testNoRewriteForImagesWithSrcsetAttrSet() {
        $img = $this->makeImage('src', 20, 30);
        $img->setAttribute('srcset', 'val');
        $this->filter->transformHTMLDOM($this->dom);
        $this->assertEquals('src', $img->getAttribute('src'));
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
        $this->assertArrayHasKey('referrer', $query);
        $this->assertEquals(self::BASE_URL, $query['referrer']);
        $this->assertArrayHasKey('token', $query);
        $this->assertNotEmpty($query['token']);

        unset ($query['referrer']);
        unset ($query['token']);

        $this->assertEquals($expectedParams, $query);
        
    }



}
