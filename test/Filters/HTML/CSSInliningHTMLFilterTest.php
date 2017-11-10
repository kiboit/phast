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

    /**
     * @dataProvider urlProvider
     */
    public function testCSSContentsRelativeURLsRewriting($input, $output) {
        $this->makeLink($this->head, "url($input)", '/css/test.css');
        $this->makeLink($this->head, "url('$input')", '/css/test2.css');
        $this->makeLink($this->head, "url(\"$input\")", '/css/test3.css');

        $this->filter->transformHTMLDOM($this->dom);

        $styles = $this->getTheStyles();

        $this->assertEquals("url($output)", $styles[0]->textContent);
        $this->assertEquals("url('$output')", $styles[1]->textContent);
        $this->assertEquals("url(\"$output\")", $styles[2]->textContent);
    }

    public function urlProvider() {
        return [
            [
                'http://' . self::BASE_URL . '/style.css',
                'http://' . self::BASE_URL . '/style.css'
            ],
            [
                '/style.css',
                '/style.css'
            ],
            [
                'style.css',
                '/css/style.css'
            ],
            [
                'http://cross-site.org/css/style.css',
                'http://cross-site.org/css/style.css'
            ],
            [
                'data:abcd',
                'data:abcd'
            ]
        ];
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

    public function testMinifying() {
        $this->makeLink($this->head, 'a-tag { prop: 12% }');
        $this->filter->transformHTMLDOM($this->dom);
        $styles = $this->getTheStyles();
        $this->assertEquals('a-tag{prop:12%}', $styles[0]->textContent);
    }

    public function testInliiningImports() {
        $css = <<<EOS
@import 'file1';
@import "file2";
@import url("file3");
@import url('file4');


the-style-itself {
    directive: true;
}
EOS;
        $this->files['/file1'] = 'the-file-1';
        $this->files['/file2'] = 'the-file-2';
        $this->files['/file3'] = 'the-file-3';
        $this->files['/file4'] = 'the-file-4';

        $this->makeLink($this->head, $css);
        $this->filter->transformHTMLDOM($this->dom);
        $styles = $this->getTheStyles();

        $this->assertCount(5, $styles);

        $this->assertEquals('the-file-1', $styles[0]->textContent);
        $this->assertEquals('the-file-2', $styles[1]->textContent);
        $this->assertEquals('the-file-3', $styles[2]->textContent);
        $this->assertEquals('the-file-4', $styles[3]->textContent);
        $this->assertEquals('the-style-itself{directive:true;}', $styles[4]->textContent);
    }

    public function testTransformingUnreachableStylesToLinks() {
        $css = '@import "some-file"; the-style-itself';
        $this->makeLink($this->head, $css);
        $this->filter->transformHTMLDOM($this->dom);

        $this->assertEquals(2, $this->head->childNodes->length);

        $link = $this->head->childNodes->item(0);
        $style = $this->head->childNodes->item(1);
        $this->assertEquals('link', $link->tagName);
        $this->assertEquals('stylesheet', $link->getAttribute('rel'));
        $this->assertEquals('/some-file', $link->getAttribute('href'));
        $this->assertEquals('style', $style->tagName);
        $this->assertEquals('the-style-itself', $style->textContent);
    }

    public function testInliningNestedStyles() {
        $css = '@import "file1"; root';
        $this->files['/file1'] = '@import "file2"; sub1';
        $this->files['/file2'] = '@import "file3";  sub2';
        $this->files['/file3'] = 'we-should-not-see-this';
        $this->makeLink($this->head, $css);

        $this->filter->transformHTMLDOM($this->dom);

        $children = $this->head->childNodes;
        $this->assertEquals(4, $children->length);

        $link = $children->item(0);
        $sub2 = $children->item(1);
        $sub1 = $children->item(2);
        $root = $children->item(3);

        $this->assertEquals('link', $link->tagName);
        $this->assertEquals('stylesheet', $link->getAttribute('rel'));
        $this->assertEquals('/file3', $link->getAttribute('href'));
        $this->assertEquals('style', $sub2->tagName);
        $this->assertEquals('sub2', $sub2->textContent);
        $this->assertEquals('style', $sub1->tagName);
        $this->assertEquals('sub1', $sub1->textContent);
        $this->assertEquals('style', $root->tagName);
        $this->assertEquals('root', $root->textContent);
    }

    public function testInliningOneFileOnlyOnce() {
        $css = '@import "file1"; root';
        $this->files['/file1'] = '@import "file2"; sub1';
        $this->files['/file2'] = '@import "file1"; sub2';
        $this->makeLink($this->head, $css);

        $this->filter->transformHTMLDOM($this->dom);

        $children = $this->head->childNodes;
        $this->assertEquals(3, $children->length);

        $this->assertEquals('sub2', $children->item(0)->textContent);
        $this->assertEquals('sub1', $children->item(1)->textContent);
        $this->assertEquals('root', $children->item(2)->textContent);
    }

    public function testKeepingMediaTypes() {
        $css = '@import "something" projection, print; @import "something-else" media and non-media;';
        $link = $this->makeLink($this->head, $css);
        $link->setAttribute('media', 'some, other, screen');
        $this->filter->transformHTMLDOM($this->dom);

        $medias = array_map(function (\DOMElement $item) {
            return $item->getAttribute('media');
        }, iterator_to_array($this->head->childNodes));

        $this->assertEquals('some, other, screen', $medias[0]);
        $this->assertEquals('some, other, screen', $medias[1]);
        $this->assertEquals('some, other, screen', $medias[2]);

    }

    public function testNotAddingNonSenceMedia() {
        $css = '@import "something"; the-css';
        $this->makeLink($this->head, $css);
        $this->filter->transformHTMLDOM($this->dom);

        $elements = $this->head->childNodes;
        $this->assertFalse($elements->item(0)->hasAttribute('media'));
        $this->assertFalse($elements->item(1)->hasAttribute('media'));
    }

    public function testNotInliningImportsInComments() {
        $css = '/* @import "stuff" ; */ the-css';
        $this->makeLink($this->head, $css);
        $this->filter->transformHTMLDOM($this->dom);

        $elements = $this->head->childNodes;
        $this->assertEquals(1, $elements->length);
        $this->assertEquals('the-css', $elements->item(0)->textContent);
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
