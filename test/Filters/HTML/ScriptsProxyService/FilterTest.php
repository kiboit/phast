<?php

namespace Kibo\Phast\Filters\HTML\ScriptsProxyService;

use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Filters\HTML\HTMLFilterTestCase;
use Kibo\Phast\Retrievers\Retriever;

class FilterTest extends HTMLFilterTestCase {

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $retriever;

    /**
     * @var Filter
     */
    private $filter;

    private $config = [
        'match' => [
            '/example\.com/',
            '/test\.com/'
        ],
        'serviceUrl' => 'script-proxy.php',
        'urlRefreshTime' => 7200
    ];

    private $modTime;

    public function setUp() {
        parent::setUp();
        $this->modTime = false;


        $this->retriever = $this->createMock(Retriever::class);
        $this->retriever->method('getLastModificationTime')
            ->willReturnCallback(function () {
                return $this->modTime;
            });

        $functions = new ObjectifiedFunctions();
        $functions->time = function () {
            return $this->config['urlRefreshTime'] * 2.5;
        };
        $this->filter = new Filter(
            $this->config,
            $this->retriever,
            $functions
        );
    }

    public function testRewrite() {
        $urls = [
            'http://example.com/script.js',
            'http://test.com/script.js',
            self::BASE_URL . '/rewrite.js',
            'http://example.com/script1.cs',
            'http://norewrite.com/script.js',
        ];

        $rewrite1 = $this->dom->createElement('script');
        $rewrite1->setAttribute('type', 'application/javascript');
        $rewrite1->setAttribute('src', $urls[0]);
        $rewrite2 = $this->dom->createElement('script');
        $rewrite2->setAttribute('src', $urls[1]);
        $rewrite3 = $this->dom->createElement('script');
        $rewrite3->setAttribute('src', $urls[2]);
        $noRewrite1 = $this->dom->createElement('script');
        $noRewrite1->setAttribute('src', $urls[3]);
        $noRewrite1->setAttribute('type', 'application/coffeescript');
        $noRewrite2 = $this->dom->createElement('script');
        $noRewrite2->setAttribute('src', $urls[4]);

        $this->head->appendChild($rewrite1);
        $this->head->appendChild($rewrite2);
        $this->head->appendChild($rewrite3);
        $this->head->appendChild($noRewrite1);
        $this->head->appendChild($noRewrite2);

        $this->filter->transformHTMLDOM($this->dom);

        foreach ([$rewrite1, $rewrite2, $rewrite3] as $i => $script) {
            $url = parse_url($script->getAttribute('src'));
            $this->assertEquals('script-proxy.php', $url['path']);
            $query = [];
            parse_str($url['query'], $query);
            $this->assertArrayHasKey('src', $query);
            $this->assertArrayHasKey('cacheMarker', $query);
            $this->assertEquals($urls[$i], $query['src']);
            $this->assertEquals(2, $query['cacheMarker']);
        }

        $this->assertEquals($urls[3], $noRewrite1->getAttribute('src'));
        $this->assertEquals($urls[4], $noRewrite2->getAttribute('src'));
    }

    public function testSettingLastModifiedTimeForCacheMarker() {
        $script = $this->dom->createElement('script');
        $script->setAttribute('src', self::BASE_URL . '/rewrite.js');
        $this->head->appendChild($script);
        $this->modTime = 123;
        $this->filter->transformHTMLDOM($this->dom);


        $url = parse_url($script->getAttribute('src'));
        $query = [];
        parse_str($url['query'], $query);
        $this->assertEquals(123, $query['cacheMarker']);
    }

    public function testRewriteSrcWithSpaces() {
        $script = $this->dom->createElement('script');
        $script->setAttribute('src', ' /hello/ ');

        $this->head->appendChild($script);

        $this->filter->transformHTMLDOM($this->dom);

        $this->assertContains('%2Fhello%2F', $script->getAttribute('src'));
        $this->assertNotContains('+', $script->getAttribute('src'));
        $this->assertNotContains('%20', $script->getAttribute('src'));
    }

    public function testRespectingBaseTag() {
        $this->addBaseTag('/new-root/');
        $script = $this->dom->createElement('script');
        $script->setAttribute('src', 'the-script.js');
        $this->head->appendChild($script);
        $this->filter->transformHTMLDOM($this->dom);

        $url = parse_url($script->getAttribute('src'));
        $this->assertEquals('script-proxy.php', $url['path']);
        $query = [];
        parse_str($url['query'], $query);
        $this->assertArrayHasKey('src', $query);
        $this->assertArrayHasKey('cacheMarker', $query);
        $this->assertEquals(self::BASE_URL . '/new-root/the-script.js', $query['src']);
        $this->assertEquals(2, $query['cacheMarker']);
    }

    public function testInjectScript() {
        $script = $this->dom->createElement('script');
        $this->head->appendChild($script);

        $this->filter->transformHTMLDOM($this->dom);

        $scripts = iterator_to_array($this->dom->getElementsByTagName('script'));
        $this->assertEquals(1, sizeof($scripts));
        $this->assertSame($script, $scripts[0]);

        $scripts = $this->dom->getPhastJavaScripts();
        $this->assertCount(1, $scripts);
        $this->assertStringEndsWith('ScriptsProxyService/rewrite-function.js', $scripts[0]->getFilename());
        $this->assertTrue($scripts[0]->hasConfig());
        $this->assertEquals('script-proxy-service', $scripts[0]->getConfigKey());
        $config = $scripts[0]->getConfig();
        $this->assertEquals($this->config['serviceUrl'], $config['serviceUrl']);
        $this->assertEquals($this->config['urlRefreshTime'], $config['urlRefreshTime']);
        $this->assertEquals($this->config['match'], $config['whitelist']);
    }

    public function testDontInjectScriptForNothing() {
        $this->filter->transformHTMLDOM($this->dom);
        $this->assertEmpty($this->dom->getPhastJavaScripts());
    }

    public function testDontInjectScriptForNonJS() {
        $script = $this->dom->createElement('script');
        $script->setAttribute('type', 'nonsense');
        $this->head->appendChild($script);

        $this->filter->transformHTMLDOM($this->dom);

        $scripts = iterator_to_array($this->dom->getElementsByTagName('script'));
        $this->assertEquals(1, sizeof($scripts));
        $this->assertSame($script, $scripts[0]);

        $this->assertEmpty($this->dom->getPhastJavaScripts());
    }


}
