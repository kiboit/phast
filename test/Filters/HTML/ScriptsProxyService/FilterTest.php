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

        $rewrite1 = $this->makeMarkedElement('script');
        $rewrite1->setAttribute('type', 'application/javascript');
        $rewrite1->setAttribute('src', $urls[0]);
        $rewrite2 = $this->makeMarkedElement('script');
        $rewrite2->setAttribute('src', $urls[1]);
        $rewrite3 = $this->makeMarkedElement('script');
        $rewrite3->setAttribute('src', $urls[2]);
        $noRewrite1 = $this->makeMarkedElement('script');
        $noRewrite1->setAttribute('src', $urls[3]);
        $noRewrite1->setAttribute('type', 'application/coffeescript');
        $noRewrite2 = $this->makeMarkedElement('script');
        $noRewrite2->setAttribute('src', $urls[4]);

        $this->head->appendChild($rewrite1);
        $this->head->appendChild($rewrite2);
        $this->head->appendChild($rewrite3);
        $this->head->appendChild($noRewrite1);
        $this->head->appendChild($noRewrite2);

        $this->applyFilter();
        $rewrite1 = $this->getMatchingElement($rewrite1);
        $rewrite2 = $this->getMatchingElement($rewrite2);
        $rewrite3 = $this->getMatchingElement($rewrite3);
        $noRewrite1 = $this->getMatchingElement($noRewrite1);
        $noRewrite2 = $this->getMatchingElement($noRewrite2);

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
        $script = $this->makeMarkedElement('script');
        $script->setAttribute('src', self::BASE_URL . '/rewrite.js');
        $this->head->appendChild($script);
        $this->modTime = 123;

        $this->applyFilter();

        $script = $this->getMatchingElement($script);
        $url = parse_url($script->getAttribute('src'));
        $query = [];
        parse_str($url['query'], $query);
        $this->assertEquals(123, $query['cacheMarker']);
    }

    public function testRewriteSrcWithSpaces() {
        $script = $this->makeMarkedElement('script');
        $script->setAttribute('src', ' /hello/ ');

        $this->head->appendChild($script);

        $this->applyFilter();
        $script = $this->getMatchingElement($script);

        $this->assertContains('%2Fhello%2F', $script->getAttribute('src'));
        $this->assertNotContains('+', $script->getAttribute('src'));
        $this->assertNotContains('%20', $script->getAttribute('src'));
    }

    public function testRespectingBaseTag() {
        $this->addBaseTag('/new-root/');
        $script = $this->makeMarkedElement('script');
        $script->setAttribute('src', 'the-script.js');
        $this->head->appendChild($script);

        $this->applyFilter();
        $script = $this->getMatchingElement($script);

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
        $script = $this->makeMarkedElement('script');
        $this->head->appendChild($script);

        $this->applyFilter();

        $this->assertMatchingElementExists($script);
        $this->assertHasCompiled('ScriptsProxyService/rewrite-function.js');

        $expectedConfig = $this->config;
        $expectedConfig['whitelist'] = $expectedConfig['match'];
        unset ($expectedConfig['match']);
        $this->assertCompiledConfigEqauls($expectedConfig, 'script-proxy-service');
    }

    public function testDontInjectScriptForNothing() {
        $this->applyFilter();
        $this->assertHasNotCompiledScripts();
    }

    public function testDontInjectScriptForNonJS() {
        $script = $this->makeMarkedElement('script');
        $script->setAttribute('type', 'nonsense');
        $this->head->appendChild($script);

        $this->applyFilter();

        $this->assertMatchingElementExists($script);
        $this->assertHasNotCompiledScripts();
    }


}
