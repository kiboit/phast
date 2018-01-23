<?php

namespace Kibo\Phast\Filters\HTML\CSSInlining;

use Kibo\Phast\Common\CSSMinifier;
use Kibo\Phast\Common\DOMDocument;
use Kibo\Phast\Filters\HTML\HTMLFilterTestCase;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\ValueObjects\URL;

class FilterTest extends HTMLFilterTestCase {

    const SERVICE_URL = self::BASE_URL . '/service.php';

    const URL_REFRESH_TIME = 7200;

    private $files;

    /**
     * @var Optimizer
     */
    private $optimizerMock;

    /**
     * @var Filter
     */
    private $filter;

    public function setUp() {
        parent::setUp();

        $this->files = [];
        $this->optimizerMock = null;

        $signature = $this->createMock(ServiceSignature::class);
        $signature->method('sign')
            ->willReturn('the-token');

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

        $optimizerFactory = $this->createMock(OptimizerFactory::class);
        $optimizerFactory->expects($this->once())
            ->method('makeForDocument')
            ->with($this->dom)
            ->willReturnCallback(function (DOMDocument $document) {
                return is_null($this->optimizerMock)
                       ? (new OptimizerFactory())->makeForDocument($document)
                       : $this->optimizerMock;
            });

        $this->filter = new Filter(
            $signature,
            URL::fromString(self::BASE_URL),
            [
                'whitelist' => [
                    '~' . preg_quote(self::BASE_URL) . '~',
                    '~https?://fonts\.googleapis\.com~' => [
                        'ieCompatible' => false
                    ]
                ],
                'serviceUrl' => self::SERVICE_URL,
                'urlRefreshTime' => self::URL_REFRESH_TIME
            ],
            $retriever,
            $optimizerFactory,
            new CSSMinifier()
        );
    }

    public function testInliningCSS() {

        $this->makeLink($this->head, 'the-file-contents', '/the-file-1.css');
        $this->makeLink($this->body, 'the-file-2-contents', '/the-file-2.css');

        $this->body->appendChild(
            $this->dom->createElement('div')
        );

        $this->optimizerMock = $this->createMock(Optimizer::class);
        $this->optimizerMock->expects($this->exactly(2))
            ->method('optimizeCSS')
            ->withConsecutive(['the-file-contents'], ['the-file-2-contents'])
            ->willReturnArgument(0);

        $this->filter->transformHTMLDOM($this->dom);


        $styles = $this->getTheStyles();

        $this->assertCount(2, $styles);

        $this->assertEquals('the-file-contents', $styles[0]->textContent);
        $this->assertEquals('the-file-2-contents', $styles[1]->textContent);

        $this->assertSame($this->head, $styles[0]->parentNode);
        $this->assertSame($this->body, $styles[1]->parentNode);
        $this->assertSame($this->body->childNodes[0], $styles[1]);

        $this->assertTrue($styles[0]->hasAttribute('data-phast-href'));
        $this->assertTrue($styles[1]->hasAttribute('data-phast-href'));
        $this->assertEquals(self::BASE_URL . '/the-file-1.css', $styles[0]->getAttribute('data-phast-href'));
        $this->assertEquals(self::BASE_URL . '/the-file-2.css', $styles[1]->getAttribute('data-phast-href'));

        $this->assertEquals(1, $this->dom->getElementsByTagName('script')->length);
    }

    public function testNotInliningOnOptimizationError() {
        $link = $this->makeLink($this->head, 'the-file-contents', '/the-path');
        $this->optimizerMock = $this->createMock(Optimizer::class);
        $this->optimizerMock->expects($this->once())
            ->method('optimizeCSS')
            ->willReturn(null);
        $this->filter->transformHTMLDOM($this->dom);

        $this->assertEquals(0, $this->dom->getElementsByTagName('script')->length);
        $theLinks = $this->dom->getElementsByTagName('link');
        $this->assertEquals(1, $theLinks->length);
        $this->assertSame($link, $theLinks[0]);
        $this->assertEquals('/the-path', $link->getAttribute('href'));
        $this->assertEquals('stylesheet', $link->getAttribute('rel'));

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
        $this->makeLink($this->head, 'css', self::BASE_URL . '/the-css.css');
        unset ($this->files[self::BASE_URL . '/the-css.css']);

        $this->filter->transformHTMLDOM($this->dom);

        $this->assertEmpty($this->getTheStyles());
        $this->assertEquals(1, $this->head->childNodes->length);

        $newLink = $this->head->childNodes[0];
        $this->assertEquals('link', $newLink->tagName);

        $expectedQuery = [
            'src' => self::BASE_URL . '/the-css.css',
            'cacheMarker' => floor(time() / self::URL_REFRESH_TIME),
            'token' => 'the-token'
        ];
        $expectedUrl = self::SERVICE_URL . '?' . http_build_query($expectedQuery);
        $this->assertEquals($expectedUrl, $newLink->getAttribute('href'));
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
        $this->makeLink($this->head, 'css3');
        $this->makeLink($this->head, 'css4', 'https://fonts.googleapis.com/missing');
        unset ($this->files['https://fonts.googleapis.com/missing']);

        $this->filter->transformHTMLDOM($this->dom);

        $notAllowedLink = $this->head->childNodes->item(0);
        $this->assertEquals('link', $notAllowedLink->tagName);
        $this->assertEquals('https://not-allowed.com/css', $notAllowedLink->getAttribute('href'));
        $this->assertFalse($notAllowedLink->hasAttribute('data-phast-ie-fallback-url'));
        $this->assertTrue($notAllowedLink->hasAttribute('data-phast-nested-inlined'));

        $import = $this->head->childNodes->item(1);
        $this->assertEquals('style', $import->tagName);
        $this->assertEquals('the-import', $import->textContent);
        $this->assertFalse($import->hasAttribute('data-phast-ie-fallback-url'));
        $this->assertTrue($import->hasAttribute('data-phast-nested-inlined'));

        $ie = $this->head->childNodes->item(2);
        $this->assertEquals('style', $ie->tagName);
        $this->assertEquals('css1', $ie->textContent);
        $this->assertFalse($ie->hasAttribute('data-phast-nested-inlined'));
        $this->assertEquals('https://fonts.googleapis.com/css1', $ie->getAttribute('data-phast-ie-fallback-url'));

        $nonIe = $this->head->childNodes->item(3);
        $this->assertFalse($nonIe->hasAttribute('data-phast-nested-inlined'));
        $this->assertFalse($nonIe->hasAttribute('data-phast-ie-fallback-url'));

        $ieLink = $this->head->childNodes->item(4);
        $this->assertEquals(
            'https://fonts.googleapis.com/missing',
            $ieLink->getAttribute('data-phast-ie-fallback-url')
        );

        $script = $this->body->childNodes->item(0);
        $this->assertEquals('script', $script->tagName);
        $this->assertTrue($script->hasAttribute('data-phast-no-defer'));
    }

    /**
     * @dataProvider whitelistedImportProvider
     */
    public function testWhitelistedImport($importFormat, $importUrl) {
        $css = '
            ' . sprintf($importFormat, $importUrl) . '
            body { color: red; }
        ';
        $this->makeLink($this->head, $css);
        $this->filter->transformHTMLDOM($this->dom);

        $this->assertEquals(2, $this->head->childNodes->length);

        $elements = iterator_to_array($this->head->childNodes);

        $link = array_shift($elements);
        $this->assertEquals('link', $link->tagName);
        $this->assertContains('service.php', $link->getAttribute('href'));
        $this->assertContains($importUrl, urldecode($link->getAttribute('href')));

        $style = array_shift($elements);
        $this->assertEquals('style', $style->tagName);
        $this->assertNotContains($importUrl, $style->textContent);
        $this->assertContains('red', $style->textContent);
    }

    public function whitelistedImportProvider() {
        $urls = [
            'https://fonts.googleapis.com/css1',
            'https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,700italic,400,300,700',
            '//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,700italic,400,300,700'
        ];

        $formats = [
            '@import "%s";',
            "@import '%s';",
            '@import url(%s);',
            '@import url("%s");',
            "@import url('%s');"
        ];

        foreach ($urls as $url) {
            foreach ($formats as $format) {
                yield [$format, $url];
            }
        }
    }

    public function testIEHackWithImport() {
        $css = '
            @import "https://fonts.googleapis.com/css1";
            body { color: red; }
        ';
        $this->makeLink($this->head, $css);
        $this->files['https://fonts.googleapis.com/css1'] = 'hello-world;';
        $this->filter->transformHTMLDOM($this->dom);

        $this->assertEquals(2, $this->head->childNodes->length);
        $this->assertEquals('style', $this->head->childNodes->item(0)->tagName);
        $this->assertEquals('style', $this->head->childNodes->item(1)->tagName);
    }

    public function testInlineImportInStyle() {
        $style = $this->dom->createElement('style');
        $style->textContent = '@import url(/test); moar;';
        $this->head->appendChild($style);

        $this->files['/test'] = 'hello';

        $this->filter->transformHTMLDOM($this->dom);

        $this->assertEquals(2, $this->head->childNodes->length);
        $this->assertEquals('hello', $this->head->childNodes->item(0)->textContent);
        $this->assertEquals('moar;', $this->head->childNodes->item(1)->textContent);
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
