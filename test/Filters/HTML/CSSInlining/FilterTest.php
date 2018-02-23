<?php

namespace Kibo\Phast\Filters\HTML\CSSInlining;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\Filters\HTML\HTMLFilterTestCase;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Element;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\Services\ServiceFilter;
use Kibo\Phast\ValueObjects\URL;

class FilterTest extends HTMLFilterTestCase {

    const SERVICE_URL = self::BASE_URL . '/service.php';

    const URL_REFRESH_TIME = 7200;

    private $retrieverLastModificationTime;

    private $files;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $optimizerMock;

    /**
     * @var int
     */
    private $cssFilterCalledTimes;

    /**
     * @var array
     */
    private $config;

    public function setUp() {
        parent::setUp();

        $this->retrieverLastModificationTime = false;
        $this->files = [];
        $this->optimizerMock = null;
        $this->cssFilterCalledTimes = 0;
        $this->config = [
            'whitelist' => [
                '~' . preg_quote(self::BASE_URL) . '~',
                '~https?://fonts\.googleapis\.com~' => [
                    'ieCompatible' => false
                ]
            ],
            'serviceUrl' => self::SERVICE_URL,
            'urlRefreshTime' => self::URL_REFRESH_TIME,
            'optimizerSizeDiffThreshold' => -1
        ];
    }

    public function testInliningCSS() {

        $this->makeLink($this->head, 'the-file-contents', '/the-file-1.css');
        $this->makeLink($this->body, 'the-file-2-contents', '/the-file-2.css');

        $this->body->appendChild(
            $this->makeMarkedElement('div')
        );

        $this->optimizerMock = $this->createMock(Optimizer::class);
        $this->optimizerMock->expects($this->exactly(2))
            ->method('optimizeCSS')
            ->withConsecutive(['the-file-contents'], ['the-file-2-contents'])
            ->willReturnArgument(0);

        $this->applyFilter();


        $styles = $this->getTheStyles();

        $this->assertCount(2, $styles);

        $this->assertEquals('the-file-contents', $styles[0]->textContent);
        $this->assertEquals('the-file-2-contents', $styles[1]->textContent);

        $this->assertSame($this->head, $styles[0]->parentNode);
        $this->assertSame($this->body->firstChild, $styles[1]);

        $this->assertTrue($styles[0]->hasAttribute('data-phast-href'));
        $this->assertTrue($styles[1]->hasAttribute('data-phast-href'));

        $expectedUrl1 = self::SERVICE_URL . '?src=' . urlencode(self::BASE_URL . '/the-file-1.css');
        $expectedUrl2 = self::SERVICE_URL . '?src=' . urlencode(self::BASE_URL . '/the-file-2.css');
        $this->assertStringStartsWith($expectedUrl1, $styles[0]->getAttribute('data-phast-href'));
        $this->assertStringStartsWith($expectedUrl2, $styles[1]->getAttribute('data-phast-href'));
        $this->assertContains('&strip-imports', $styles[0]->getAttribute('data-phast-href'));
        $this->assertContains('&strip-imports', $styles[1]->getAttribute('data-phast-href'));

        $this->assertHasCompiled('CSSInlining/inlined-css-retriever.js');
    }

    public function testCallingTheFilterOnBothStylesAndLinks() {
        $this->makeLink($this->head, 'the-file-contents');
        $this->head->appendChild($this->makeMarkedElement('style'));
        $this->applyFilter();
        $this->assertEquals(2, $this->cssFilterCalledTimes);
    }

    public function testNotAddingPhastHrefToExistingStyleTags() {
        $style = $this->makeMarkedElement('style');
        $this->head->appendChild($style);
        $this->applyFilter();
        $styles = $this->dom->getElementsByTagName('style');
        $this->assertFalse($styles->item(0)->hasAttribute('data-phast-href'));
        $this->assertHasNotCompiledScripts();
    }

    public function testNotInliningOnOptimizationError() {
        $link = $this->makeLink($this->head, 'the-file-contents', '/the-path');
        $this->optimizerMock = $this->createMock(Optimizer::class);
        $this->optimizerMock->expects($this->once())
            ->method('optimizeCSS')
            ->willReturn(null);
        $this->applyFilter();

        $this->assertHasNotCompiledScripts();

        $link = $this->getMatchingElement($link);
        $this->assertEquals('/the-path', $link->getAttribute('href'));
        $this->assertEquals('stylesheet', $link->getAttribute('rel'));
    }

    public function testInliningOriginalIfBellowThreshold() {
        $this->config['optimizerSizeDiffThreshold'] = 100;
        $originalContent = str_repeat('a', 200);
        $optimizedContent = str_repeat('b', 150);
        $this->makeLink($this->head, $originalContent);
        $this->optimizerMock = $this->createMock(Optimizer::class);
        $this->optimizerMock->expects($this->once())
            ->method('optimizeCSS')
            ->with($originalContent)
            ->willReturn($optimizedContent);
        $this->applyFilter();
        $style = $this->head->getElementsByTagName('style')->item(0);
        $this->assertEquals($originalContent, $style->textContent);
        $this->assertFalse($style->hasAttribute('data-phast-href'));
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

        $this->applyFilter();

        $this->assertEmpty($this->getTheStyles());

        $headElements = $this->head->childNodes;
        $this->assertElementsMatch($badRel, $headElements->item(0));
        $this->assertElementsMatch($noRel, $headElements->item(1));
        $this->assertElementsMatch($noHref, $headElements->item(2));
        $this->assertElementsMatch($crossSite, $headElements->item(3));
    }

    public function testRedirectingToProxyServiceOnReadError() {
        $this->makeLink($this->head, 'css', self::BASE_URL . '/the-css.css');
        unset ($this->files[self::BASE_URL . '/the-css.css']);

        $this->applyFilter();

        $this->assertEmpty($this->getTheStyles());

        $headElements = $this->head->childNodes;
        $this->assertEquals(1, $headElements->length);

        $newLink = $headElements->item(0);
        $this->assertEquals('link', $newLink->tagName);

        $expectedQuery = [
            'src' => self::BASE_URL . '/the-css.css',
            'cacheMarker' => floor(time() / self::URL_REFRESH_TIME),
            'token' => 'the-token'
        ];
        $expectedUrl = self::SERVICE_URL . '?' . http_build_query($expectedQuery);
        $this->assertEquals($expectedUrl, $newLink->getAttribute('href'));
    }

    public function testSettingRightCacheMarkerOnLocalScripts() {
        $this->retrieverLastModificationTime = 123;
        $this->makeLink($this->head, 'css');
        $this->applyFilter();

        $style = $this->head->getElementsByTagName('style')->item(0);
        $url = parse_url($style->getAttribute('data-phast-href'));
        $query = [];
        parse_str($url['query'], $query);
        $this->assertEquals(123, $query['cacheMarker']);
    }

    public function testMinifyingBeforeOptimizing() {
        $this->optimizerMock = $this->createMock(Optimizer::class);
        $this->optimizerMock->expects($this->once())
            ->method('optimizeCSS')
            ->willReturnCallback(function ($css) {
                $this->assertEquals(1, $this->cssFilterCalledTimes);
                return $css;
            });

        $this->makeLink($this->head, 'some-css');
        $this->applyFilter();
    }

    public function testInliningImports() {
        $formats = [
            "'%s'",
            '"%s"',
            'url(%s)',
            "url('%s')",
            'url("%s")',
            '" %s "',
            'url( %s )',
            "url(' %s ')",
        ];

        $css = [];
        foreach ($formats as $i => $fmt) {
            $css[] = sprintf("@import %s;", sprintf($fmt, "file$i"));
            $this->files["/file$i"] = "the-file-$i";
        }
        $css[] = 'the-style-itself{directive: true;}';
        $css = implode("\n", $css);

        $this->makeLink($this->head, $css);
        $this->applyFilter();
        $styles = $this->getTheStyles();

        $this->assertCount(sizeof($formats) + 1, $styles);

        foreach ($formats as $i => $fmt) {
            $this->assertEquals("the-file-$i", $styles[$i]->textContent);
        }

        $this->assertEquals("the-style-itself{directive: true;}", trim($styles[sizeof($formats)]->textContent));
    }

    public function testInliningNestedStyles() {
        $css = '@import "file1"; root';
        $this->files['/file1'] = '@import "file2"; sub1';
        $this->files['/file2'] = '@import "file3"; sub2';
        $this->files['/file3'] = 'we-should-not-see-this';
        $this->makeLink($this->head, $css);

        $this->applyFilter();

        $headElements = $this->head->childNodes;
        $this->assertEquals(4, $headElements->length);

        $link = $headElements->item(0);
        $sub2 = $headElements->item(1);
        $sub1 = $headElements->item(2);
        $root = $headElements->item(3);


        $this->assertEquals('link', $link->tagName);
        $this->assertEquals('stylesheet', $link->getAttribute('rel'));
        $this->assertEquals(self::BASE_URL . '/file3', $link->getAttribute('href'));
        $this->assertEquals('style', $sub2->tagName);
        $this->assertEquals(' sub2', $sub2->textContent);
        $this->assertEquals('style', $sub1->tagName);
        $this->assertEquals(' sub1', $sub1->textContent);
        $this->assertEquals('style', $root->tagName);
        $this->assertEquals(' root', $root->textContent);
    }

    public function testInliningOneFileOnlyOnce() {
        $css = '@import "file1"; root';
        $this->files['/file1'] = '@import "file2"; sub1';
        $this->files['/file2'] = '@import "file1"; sub2';
        $this->makeLink($this->head, $css);

        $this->applyFilter();

        $children = $this->head->childNodes;
        $this->assertEquals(3, $children->length);

        $this->assertEquals(' sub2', $children->item(0)->textContent);
        $this->assertEquals(' sub1', $children->item(1)->textContent);
        $this->assertEquals(' root', $children->item(2)->textContent);
    }

    public function testKeepingMediaTypes() {
        $css = '@import "something" projection, print; @import "something-else" media and non-media;';
        $link = $this->makeLink($this->head, $css);
        $link->setAttribute('media', 'some, other, screen');
        $this->applyFilter();

        $headElements = $this->head->childNodes;
        $this->assertEquals(1, $headElements->length);

        $style = $headElements->item(0);
        $this->assertEquals('style', $style->tagName);
        $this->assertEquals('some, other, screen', $style->getAttribute('media'));
        $this->assertEquals($css, $style->textContent);

    }

    public function testNotAddingNonsenseMedia() {
        $css = '@import "something"; the-css';
        $this->makeLink($this->head, $css);
        $this->applyFilter();

        $elements = $this->head->childNodes;
        $this->assertFalse($elements->item(0)->hasAttribute('media'));
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

        $this->applyFilter();

        $headElements = $this->head->childNodes;
        $notAllowedLink = $headElements->item(0);
        $this->assertEquals('link', $notAllowedLink->tagName);
        $this->assertEquals('https://not-allowed.com/css', $notAllowedLink->getAttribute('href'));
        $this->assertFalse($notAllowedLink->hasAttribute('data-phast-ie-fallback-url'));
        $this->assertTrue($notAllowedLink->hasAttribute('data-phast-nested-inlined'));

        $import = $headElements->item(1);
        $this->assertEquals('style', $import->tagName);
        $this->assertEquals('the-import', $import->textContent);
        $this->assertFalse($import->hasAttribute('data-phast-ie-fallback-url'));
        $this->assertTrue($import->hasAttribute('data-phast-nested-inlined'));

        $ie = $headElements->item(2);
        $this->assertEquals('style', $ie->tagName);
        $this->assertEquals('  css1', $ie->textContent);
        $this->assertFalse($ie->hasAttribute('data-phast-nested-inlined'));
        $this->assertEquals('https://fonts.googleapis.com/css1', $ie->getAttribute('data-phast-ie-fallback-url'));

        $nonIe = $headElements->item(3);
        $this->assertFalse($nonIe->hasAttribute('data-phast-nested-inlined'));
        $this->assertFalse($nonIe->hasAttribute('data-phast-ie-fallback-url'));

        $ieLink = $headElements->item(4);
        $this->assertEquals(
            'https://fonts.googleapis.com/missing',
            $ieLink->getAttribute('data-phast-ie-fallback-url')
        );

        $this->assertHasCompiled('CSSInlining/ie-fallback.js');
        $this->assertHasCompiled('CSSInlining/inlined-css-retriever.js');
    }

    /**
     * @dataProvider whitelistedImportProvider
     * @param $importFormat
     * @param $importUrl
     */
    public function testWhitelistedImport($importFormat, $importUrl) {
        $css = '
            ' . sprintf($importFormat, $importUrl) . '
            body { color: red; }
        ';
        $this->makeLink($this->head, $css);
        $this->applyFilter();

        $elements = iterator_to_array($this->head->childNodes);
        $this->assertCount(2, $elements);


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
        $this->applyFilter();


        $elements = $this->head->childNodes;
        $this->assertEquals(2, $elements->length);
        $this->assertEquals('style', $elements->item(0)->tagName);
        $this->assertEquals('style', $elements->item(1)->tagName);
    }

    public function testInlineImportInStyle() {
        $style = $this->makeMarkedElement('style');
        $style->textContent = '@import url(/test); moar;';
        $this->head->appendChild($style);

        $this->files['/test'] = 'hello';

        $this->applyFilter();

        $elements = $this->head->childNodes;
        $this->assertEquals(2, $elements->length);
        $this->assertEquals('hello', $elements->item(0)->textContent);
        $this->assertEquals(' moar;', $elements->item(1)->textContent);
    }

    public function testNotRewritingNotWhitelisted() {
        $this->makeLink($this->head, 'css', 'http://not-allowed.com');
        $this->applyFilter();

        $elements = $this->head->childNodes;
        $this->assertEquals(1, $elements->length);
        $link =  $elements->item(0);
        $this->assertEquals('link', $link->tagName);
        $this->assertEquals('http://not-allowed.com', $link->getAttribute('href'));
    }

    public function testInlineUTF8() {
        $this->markTestIncomplete('Figure out how to perform this test');
        $css = 'body { content: "ü"; }';
        $this->makeLink($this->head, $css);
        $this->applyFilter();

        $elements = $this->head->childNodes;
        $this->assertEquals(1, $elements->length);
        $this->assertContains('ü', $elements->item(0)->textContent);

        $this->assertContains('ü', $this->dom->serialize());
    }

    public function testRespectingBaseTag() {
        $this->addBaseTag('/new-root/');
        $link = $this->makeMarkedElement('link');
        $link->setAttribute('rel', 'stylesheet');
        $link->setAttribute('href', 'the-css-file.css');
        $this->head->appendChild($link);

        $this->files['/new-root/the-css-file.css'] = 'the-css-content';
        $this->applyFilter();

        $styles = $this->dom->getElementsByTagName('style');
        $this->assertEquals(1, $styles->length);
        $this->assertEquals('the-css-content', $styles->item(0)->textContent);
    }

    public function testSpaceInLinkHref() {
        $html = '<html><head><link rel="stylesheet" href=" the-css-file.css "></head><body></body></html>';

        $this->files['/the-css-file.css'] = 'the-css-content';
        $this->applyFilter($html);

        $styles = $this->dom->getElementsByTagName('style');
        $this->assertEquals(1, $styles->length);
        $this->assertEquals('the-css-content', $styles->item(0)->textContent);
    }

    protected function applyFilter($htmlInput = null, $skipResultParsing = false) {
        $signature = $this->createMock(ServiceSignature::class);
        $signature->method('sign')
            ->willReturn('the-token');

        $retriever = $this->createMock(Retriever::class);
        $retriever->method('getLastModificationTime')
            ->willReturnCallback(function () {
                return $this->retrieverLastModificationTime;
            });
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

        $cache = $this->createMock(Cache::class);
        $cache->method('get')
            ->willReturnCallback(function ($key, callable  $cb) {
                return $cb();
            });

        $optimizerFactory = $this->createMock(OptimizerFactory::class);
        $optimizerFactory->expects($this->once())
            ->method('makeForElements')
            ->willReturnCallback(function ($elements) use ($cache) {
                return is_null($this->optimizerMock)
                    ? new Optimizer($elements, $cache)
                    : $this->optimizerMock;
            });

        $cssFilter = $this->createMock(ServiceFilter::class);
        $cssFilter->method('apply')
            ->willReturnCallback(function ($css) {
                $this->cssFilterCalledTimes++;
                return $css;
            });

        $filter = new Filter(
            $signature,
            URL::fromString(self::BASE_URL),
            $this->config,
            $retriever,
            $optimizerFactory,
            $cssFilter
        );
        $this->filter = $filter;
        return parent::applyFilter($htmlInput, $skipResultParsing);
    }

    /**
     * @param \DOMElement $parent
     * @param string $content
     * @param null $url
     * @return \DOMElement
     */
    private function makeLink(\DOMElement $parent, $content = 'some-content', $url = null) {
        static $nextFileIndex = 0;
        $fileName = is_null($url) ? '/css-file-' . $nextFileIndex++ : $url;
        $link = $this->makeMarkedElement('link');
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

    private function assertElementBetween(Element $left, Element $between, Element $right) {
        $leftIndex = $this->stream->getElementIndex($left);
        $rightIndex = $this->stream->getElementIndex($right);
        $betweenIndex = $this->stream->getElementIndex($between);
        $this->assertGreaterThan($leftIndex, $betweenIndex, 'Element is on the right');
        $this->assertLessThan($rightIndex, $betweenIndex, 'Element is not the left');
    }

}
