<?php

namespace Kibo\Phast\Common;

use Kibo\Phast\ValueObjects\URL;
use PHPUnit\Framework\TestCase;

class DOMDocumentTest extends TestCase {

    /**
     * @var DOMDocument
     */
    private $dom;

    public function setUp() {
        parent::setUp();
        $this->dom = DOMDocument::makeForLocation(URL::fromString('http://phast.test'));
    }

    public function testGetBaseURLNoBaseTag() {
        $html = '<html><head></head></html>';
        $this->dom->loadHTML($html);
        $this->assertEquals('http://phast.test', $this->dom->getBaseURL()->toString());
    }

    public function testGetBaseURLWithBaseTag() {
        $html = '<html><head><base href="/the-base"></head></html>';
        $this->dom->loadHTML($html);
        $this->assertEquals('http://phast.test/the-base', $this->dom->getBaseURL()->toString());
    }

    public function testGetBaseURLWithBaseTagWithoutHref() {
        $html = '<html><head><base></head></html>';
        $this->dom->loadHTML($html);
        $this->assertEquals('http://phast.test', $this->dom->getBaseURL()->toString());
    }



}
