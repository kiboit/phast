<?php

namespace Kibo\Phast\Filters\HTML\ScriptsProxyService;

use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Filters\HTML\HTMLFilterTestCase;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\Services\ServiceRequest;

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
        ServiceRequest::setDefaultSerializationMode(ServiceRequest::FORMAT_QUERY);

        $this->modTime = false;


        $this->retriever = $this->createMock(Retriever::class);
        $this->retriever->method('getCacheSalt')
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

    /**
     * @dataProvider rewriteData
     */
    public function testRewrite($attributes, $shouldRewrite) {
        $this->modTime = 2;
        $element = $this->makeMarkedElement('script');
        foreach ($attributes as $name => $value) {
            $element->setAttribute($name, $value);
        }
        $this->head->appendChild($element);
        $this->applyFilter();
        $newElement = $this->getMatchingElement($element);
        if ($shouldRewrite) {
            $this->assertTrue($newElement->hasAttribute('data-phast-original-absolute-src'));
            $this->assertEquals(
                $newElement->getAttribute('data-phast-original-src'),
                $newElement->getAttribute('data-phast-original-absolute-src')
            );
            list ($query, $url) = $this->parseSrc($newElement);
            $this->assertEquals('script-proxy.php', $url['path']);
            $this->assertArrayHasKey('src', $query);
            $this->assertArrayHasKey('cacheMarker', $query);
            $this->assertEquals($attributes['src'], $query['src']);
            $this->assertEquals($this->modTime, $query['cacheMarker']);
        } else {
            $this->assertEquals($attributes['src'], $newElement->getAttribute('src'));
        }
    }

    public function rewriteData() {
        return [
            [['src' => 'http://example.com/script.js', 'type' => 'application/javascript'], true],
            [['src'  => 'http://test.com/script.js'], true],
            [['src'  => self::BASE_URL . '/rewrite.js'], true],
            [['src'  => 'http://example.com/script1.cs', 'type' => 'application/coffeesctipt'], false],
            [['src'  => 'http://norewrite.com/script.js'], false],
        ];
    }

    public function testSettingSameSrcForSameURLInDifferentDocs() {
        $scriptA = $this->makeMarkedElement('script');
        $scriptA->setAttribute('src', 'url-a');

        $scriptB = $this->makeMarkedElement('script');
        $scriptB->setAttribute('src', 'url-b');

        $this->head->appendChild($scriptA);
        $this->head->appendChild($scriptB);

        $this->applyFilter();

        $scriptA1 = $this->getMatchingElement($scriptA);
        $scriptB1 = $this->getMatchingElement($scriptB);

        $this->setUp();

        $scriptA = $this->makeMarkedElement('script');
        $scriptA->setAttribute('src', 'url-a');

        $scriptB = $this->makeMarkedElement('script');
        $scriptB->setAttribute('src', 'url-b');

        $this->head->appendChild($scriptB);
        $this->head->appendChild($scriptA);

        $this->applyFilter();

        $scriptA2 = $this->getMatchingElement($scriptA);
        $scriptB2 = $this->getMatchingElement($scriptB);

        $this->assertEquals($scriptA1->getAttribute('src'), $scriptA2->getAttribute('src'));
        $this->assertEquals($scriptB1->getAttribute('src'), $scriptB2->getAttribute('src'));
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
        $this->modTime = 2;
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
        $this->assertCompiledConfigEquals($expectedConfig, 'script-proxy-service');
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

    private function parseSrc(\DOMElement $script) {
        $url = parse_url($script->getAttribute('src'));
        $this->assertEquals('script-proxy.php', $url['path']);
        $query = [];
        parse_str($url['query'], $query);
        return [$query, $url];
    }

}
