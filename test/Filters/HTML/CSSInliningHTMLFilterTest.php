<?php

namespace Kibo\Phast\Filters\HTML;

use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\ValueObjects\URL;

class CSSInliningHTMLFilterTest extends HTMLFilterTestCase {

    private $files;

    /**
     * @var CSSInliningHTMLFilter
     */
    private $filter;

    public function setUp() {
        parent::setUp();

        $this->files = [];
        $retriever = $this->createMock(Retriever::class);
        $retriever->method('retrieve')
            ->willReturnCallback(function (URL $url) {
                return isset ($this->files[$url->getPath()]) ? $this->files[$url->getPath()] : false;
            });
        $this->filter = new CSSInliningHTMLFilter(URL::fromString(self::BASE_URL), $retriever);
    }

    public function testInliningCSS() {

        $this->makeLink($this->head, 'the-file-contents');
        $this->makeLink($this->body, 'the-file-2-contents');

        $this->body->appendChild(
            $this->dom->createElement('div')
        );

        $this->filter->transformHTMLDOM($this->dom);


        $styles = $this->getTheStyles();

        $this->assertCount(2, $styles);
        $this->assertEquals('the-file-contents', $styles[0]->textContent);
        $this->assertEquals('the-file-2-contents', $styles[1]->textContent);
        $this->assertSame($this->head, $styles[0]->parentNode);
        $this->assertSame($this->body, $styles[1]->parentNode);
        $this->assertSame($this->body->childNodes[0], $styles[1]);
    }

    public function testKeepingMediaAttribute() {
        $link = $this->makeLink($this->head);
        $link->setAttribute('media', 'print');

        $this->filter->transformHTMLDOM($this->dom);

        $styles = $this->getTheStyles();
        $this->assertCount(1, $styles);
        $this->assertTrue($styles[0]->hasAttribute('media'));
        $this->assertEquals('print', $styles[0]->getAttribute('media'));
    }

    public function testInliningWithCorrectRel() {
        $badRel = $this->makeLink($this->head);
        $noRel = $this->makeLink($this->head);
        $noHref = $this->makeLink($this->head);
        $crossSite = $this->makeLink($this->head);

        $badRel->setAttribute('rel', 'not-style');
        $noRel->removeAttribute('rel');
        $noHref->removeAttribute('href');
        $crossSite->setAttribute('href', 'http://www.example.com/some-file.css');

        $this->filter->transformHTMLDOM($this->dom);

        $this->assertEmpty($this->getTheStyles());
        $this->assertSame($badRel, $this->head->childNodes[0]);
        $this->assertSame($noRel, $this->head->childNodes[1]);
        $this->assertSame($noHref, $this->head->childNodes[2]);
        $this->assertSame($crossSite, $this->head->childNodes[3]);
    }

    public function testCSSContentsRelativeURLsRewriting() {
        $absoluteUrl = 'http://' . self::BASE_URL . '/style.css';
        $rootUrl = '/style.css';
        $relativeUrl = 'style.css';
        $crossSiteUrl = 'http://cross-site.org/css/style.css';

        $this->makeLink($this->head, "@import '$absoluteUrl'");
        $this->makeLink($this->head, "url('$absoluteUrl')");
        $this->makeLink($this->head, "@import \"$rootUrl\"", '/css/sheet1.css');
        $this->makeLink($this->head, "url(\"$rootUrl\")", '/css/sheet2.css');
        $this->makeLink($this->head, "@import '$relativeUrl''", '/css/sheet4.css');
        $this->makeLink($this->head, "url('$relativeUrl')", '/css/sheet5.css');
        $this->makeLink($this->head, "@import '$crossSiteUrl'", '/css/sheet6.css');
        $this->makeLink($this->head, "url('$crossSiteUrl')", '/css/sheet7.css');


        $this->filter->transformHTMLDOM($this->dom);

        $styles = $this->getTheStyles();

        $this->assertEquals("@import '$absoluteUrl'", $styles[0]->textContent);
        $this->assertEquals("url('$absoluteUrl')", $styles[1]->textContent);
        $this->assertEquals("@import \"$rootUrl\"", $styles[2]->textContent);
        $this->assertEquals("url(\"$rootUrl\")", $styles[3]->textContent);
        $this->assertEquals("@import '/css/$relativeUrl''", $styles[4]->textContent);
        $this->assertEquals("url('/css/$relativeUrl')", $styles[5]->textContent);
        $this->assertEquals("@import '$crossSiteUrl'", $styles[6]->textContent);
        $this->assertEquals("url('$crossSiteUrl')", $styles[7]->textContent);
    }

    public function testNotInliningOnReadError() {
        $theLink = $this->makeLink($this->head);
        $retriever = $this->createMock(Retriever::class);
        $retriever->method('retrieve')
              ->willReturnCallback(function () {
                  @trigger_error('An error', E_USER_WARNING);
                  return false;
              });
        $filter = new CSSInliningHTMLFilter(URL::fromString(self::BASE_URL), $retriever);
        $filter->transformHTMLDOM($this->dom);

        $this->assertEmpty($this->getTheStyles());
        $this->assertSame($this->head->childNodes[0], $theLink);
    }

    /**
     * @param \DOMElement $parent
     * @param string $content
     * @param string|null $url
     * @return \DOMElement
     */
    private function makeLink(\DOMElement $parent, $content = 'some-content', $url = null) {
        static $nextFileIndex = 0;
        $fileName = is_null($url) ? '/css-file-' . $nextFileIndex++ : $url;
        $link = $this->dom->createElement('link');
        $link->setAttribute('href', $fileName);
        $link->setAttribute('rel', 'stylesheet');
        $parent->appendChild($link);

        $this->files[$fileName] = $content;
        return $link;
    }

    /**
     * @return \DOMElement[]
     */
    private function getTheStyles() {
        return iterator_to_array($this->dom->getElementsByTagName('style'));
    }


}
