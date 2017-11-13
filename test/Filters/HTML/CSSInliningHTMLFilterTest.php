<?php

namespace Kibo\Phast\Filters\HTML;

use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\ValueObjects\URL;

class CSSInliningHTMLFilterTest extends HTMLFilterTestCase {

    const SERVICE_URL = self::BASE_URL . '/service.php';

    const URL_REFRESH_TIME = 7200;

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
                if (isset ($this->files[$url->getPath()])) {
                    return $this->files[$url->getPath()];
                }
                if (isset ($this->files[(string)$url])) {
                    return $this->files[(string)$url];
                }
                return false;
            });
        $this->filter = new CSSInliningHTMLFilter(
            URL::fromString(self::BASE_URL),
            [
                'whitelist' => [
                    '~' . preg_quote(self::BASE_URL) . '~',
                    '~https://fonts\.googleapis\.com~' => [
                        'ieCompatible' => false
                    ]
                ],
                'serviceUrl' => self::SERVICE_URL,
                'urlRefreshTime' => self::URL_REFRESH_TIME
            ],
            $retriever
        );
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
                self::BASE_URL . '/style.css',
                self::BASE_URL . '/style.css'
            ],
            [
                '/style.css',
                self::BASE_URL . '/style.css'
            ],
            [
                'style.css',
                self::BASE_URL . '/css/style.css'
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

    public function testRedirectingToProxyServiceOnReadError() {
        $theLink = $this->makeLink($this->head, 'css', self::BASE_URL . '/the-css.css');
        $retriever = $this->createMock(Retriever::class);
        $retriever->method('retrieve')
              ->willReturnCallback(function () {
                  @trigger_error('An error', E_USER_WARNING);
                  return false;
              });
        $filter = new CSSInliningHTMLFilter(
            URL::fromString(self::BASE_URL),
            [
                'whitelist' => [
                    '~' . preg_quote(self::BASE_URL) . '~',
                    '~https://fonts\.googleapis\.com~' => [
                        'ieCompatible' => false
                    ]
                ],
                'serviceUrl' => self::SERVICE_URL,
                'urlRefreshTime' => self::URL_REFRESH_TIME
            ],
            $retriever
        );
        $filter->transformHTMLDOM($this->dom);

        $this->assertEmpty($this->getTheStyles());
        $this->assertSame($this->head->childNodes[0], $theLink);

        $expectedQuery = [
            'src' => self::BASE_URL . '/the-css.css',
            'cacheMarker' => floor(time() / self::URL_REFRESH_TIME)
        ];
        $expectedUrl = self::SERVICE_URL . '?' . http_build_query($expectedQuery);
        $this->assertEquals($expectedUrl, $theLink->getAttribute('href'));

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
        $this->assertEquals(self::BASE_URL . '/file3', $link->getAttribute('href'));
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

        $this->assertEquals(1, $this->head->childNodes->length);
        $style = $this->head->childNodes->item(0);
        $this->assertEquals('style', $style->tagName);
        $this->assertEquals('some, other, screen', $style->getAttribute('media'));
        $this->assertEquals(
            '@import "'
                . self::BASE_URL
                . '/something" projection,print;@import "'
                . self::BASE_URL
                . '/something-else" media and non-media;',
            $style->textContent
        );

    }

    public function testNotAddingNonSenceMedia() {
        $css = '@import "something"; the-css';
        $this->makeLink($this->head, $css);
        $this->filter->transformHTMLDOM($this->dom);

        $elements = $this->head->childNodes;
        $this->assertFalse($elements->item(0)->hasAttribute('media'));
    }

    public function testNotInliningImportsInComments() {
        $css = '/* @import "stuff" ; */ the-css';
        $this->makeLink($this->head, $css);
        $this->filter->transformHTMLDOM($this->dom);

        $elements = $this->head->childNodes;
        $this->assertEquals(1, $elements->length);
        $this->assertEquals('the-css', $elements->item(0)->textContent);
    }

    public function testHandlingIEIncompatibilities() {
        $this->makeLink(
            $this->head,
            '@import "https://not-allowed.com/css"; @import "https://fonts.googleapis.com/css3"; css1',
            'https://fonts.googleapis.com/css1'
        );
        $this->files['https://fonts.googleapis.com/css3'] = 'the-import';
        $this->makeLink($this->head, 'css2', 'https://fonts.googleapis.com/css2');
        $this->makeLink($this->head, 'css3');
        $this->filter->transformHTMLDOM($this->dom);


        $import = $this->head->childNodes->item(0);
        $ie = $this->head->childNodes->item(1);
        $ie2 = $this->head->childNodes->item(2);
        $nonIe = $this->head->childNodes->item(3);


        $this->assertEquals('style', $import->tagName);
        $this->assertEquals('the-import', $import->textContent);
        $this->assertFalse($import->hasAttribute('data-phast-ie-fallback-url'));
        $this->assertEquals('1', $import->getAttribute('data-phast-ie-fallback-group'));

        $this->assertEquals('style', $ie->tagName);
        $this->assertEquals('@import "https://not-allowed.com/css";css1', $ie->textContent);
        $this->assertEquals('1', $import->getAttribute('data-phast-ie-fallback-group'));
        $this->assertEquals('https://fonts.googleapis.com/css1', $ie->getAttribute('data-phast-ie-fallback-url'));

        $this->assertEquals('style', $ie2->tagName);
        $this->assertEquals('css2', $ie2->textContent);
        $this->assertEquals('2', $ie2->getAttribute('data-phast-ie-fallback-group'));
        $this->assertEquals('https://fonts.googleapis.com/css2', $ie2->getAttribute('data-phast-ie-fallback-url'));

        $this->assertFalse($nonIe->hasAttribute('data-phast-ie-fallback-group'));
        $this->assertFalse($nonIe->hasAttribute('data-phast-ie-fallback-url'));
        $script = $this->body->childNodes->item(0);
        $this->assertEquals('script', $script->tagName);

    }

    public function testNotRewritingNotWhitelisted() {
        $this->makeLink($this->head, 'css', 'http://not-allowed.com');
        $this->filter->transformHTMLDOM($this->dom);

        $this->assertEquals(1, $this->head->childNodes->length);
        $link = $this->head->childNodes->item(0);
        $this->assertEquals('link', $link->tagName);
        $this->assertEquals('http://not-allowed.com', $link->getAttribute('href'));
    }

    public function testInlineUTF8() {
        $css = 'body { content: "ü"; }';
        $this->makeLink($this->head, $css);
        $this->filter->transformHTMLDOM($this->dom);

        $elements = $this->head->childNodes;
        $this->assertEquals(1, $elements->length);
        $this->assertContains('ü', $elements->item(0)->textContent);
        $this->assertContains('ü', $this->dom->saveHTML($this->dom->firstChild));
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
