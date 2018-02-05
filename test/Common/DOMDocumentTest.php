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


    public function testSerializeToHTML5() {
        $original = '<?ins v>';
        $original .= '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
        $original .= '<html><head></head><body>the-body</body></html><div>some-div</div>';
        $this->dom->loadHTML($original);
        $serialized = $this->dom->serializeToHTML5();
        $expected = "<!doctype html><html>\n<head></head>\n<body>the-body</body>\n</html>";
        $expected .= "<html><div>some-div</div></html>";
        $this->assertEquals($expected, $serialized);
    }

}
