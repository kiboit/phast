<?php

namespace Kibo\Phast\Filters\HTML\ScriptsProxyService;

use Kibo\Phast\Cache\Sqlite\Cache;
use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Filters\HTML\HTMLFilterTestCase;
use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\Services\Bundler\TokenRefMaker;
use Kibo\Phast\Services\ServiceRequest;
use Kibo\Phast\ValueObjects\URL;

class FilterTest extends HTMLFilterTestCase {
    const MODIFICATION_TIME = 1337;

    const EXPECTED_CACHE_MARKER = '1337-3';

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $retriever;

    private $config = [
        'match' => [
            '/example\.com/',
            '/test\.com/',
        ],
        'serviceUrl' => 'script-proxy.php',
        'urlRefreshTime' => 7200,
    ];

    public function setUp(): void {
        parent::setUp();
        ServiceRequest::setDefaultSerializationMode(ServiceRequest::FORMAT_QUERY);

        $signature = $this->createMock(ServiceSignature::class);
        $signature->method('sign')
            ->willReturn('the-token');

        $this->retriever = $this->createMock(LocalRetriever::class);
        $this->retriever->method('getCacheSalt')
            ->willReturnCallback(function (URL $url) {
                if (!URL::fromString($url)->isLocalTo(URL::fromString(self::BASE_URL))) {
                    return false;
                }
                return self::MODIFICATION_TIME;
            });

        $functions = new ObjectifiedFunctions();
        $functions->time = function () {
            return $this->config['urlRefreshTime'] * 2.5;
        };
        $this->filter = new Filter(
            $this->config,
            $signature,
            $this->retriever,
            new TokenRefMaker($this->createMock(Cache::class)),
            $functions
        );
    }

    /**
     * @dataProvider rewriteData
     */
    public function testRewrite($attributes, $shouldRewrite) {
        $element = $this->makeMarkedElement('script');
        foreach ($attributes as $name => $value) {
            $element->setAttribute($name, $value);
        }
        $this->head->appendChild($element);
        $this->applyFilter();
        $newElement = $this->getMatchingElement($element);
        if ($shouldRewrite) {
            $this->assertTrue($newElement->hasAttribute('data-phast-params'));
            $params = json_decode($newElement->getAttribute('data-phast-params'));
            $this->assertEquals(preg_replace('/\?.*/', '', $attributes['src']), $params->src);
            $this->assertEquals('the-token', $params->token);
            $this->assertEquals('1', $params->isScript);
            $this->assertEquals(self::EXPECTED_CACHE_MARKER, $params->cacheMarker);

            list($query, $url) = $this->parseSrc($newElement);
            $this->assertEquals('script-proxy.php', $url['path']);
            $this->assertArrayHasKey('src', $query);
            $this->assertArrayHasKey('cacheMarker', $query);
            $this->assertEquals(preg_replace('/\?.*/', '', $attributes['src']), $query['src']);
            $this->assertEquals(self::EXPECTED_CACHE_MARKER, $query['cacheMarker']);
        } else {
            $this->assertEquals($attributes['src'], $newElement->getAttribute('src'));
        }
    }

    public function rewriteData() {
        return [
            [['src' => self::BASE_URL . '/rewrite.js'], true],
            [['src' => self::BASE_URL . '/script1.cs', 'type' => 'application/coffeescript'], false],
            [['src' => self::BASE_URL . '/rewrite.js', 'type' => 'application/javascript'], true],
            [['src' => self::BASE_URL . '/rewrite.js?abc'], true],
            [['src' => 'http://example.com/script.js', 'type' => 'application/javascript'], false],
            [['src' => 'http://test.com/script.js'], false],
            [['src' => 'http://example.com/script1.cs', 'type' => 'application/coffeescript'], false],
            [['src' => 'http://norewrite.com/script.js?abc'], false],
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

        $this->applyFilter();

        $script = $this->getMatchingElement($script);
        $url = parse_url($script->getAttribute('src'));
        $query = [];
        parse_str($url['query'], $query);
        $this->assertEquals(self::EXPECTED_CACHE_MARKER, $query['cacheMarker']);
    }

    public function testRewriteSrcWithSpaces() {
        $script = $this->makeMarkedElement('script');
        $script->setAttribute('src', ' ' . self::BASE_URL . '/hello/ ');

        $this->head->appendChild($script);

        $this->applyFilter();
        $script = $this->getMatchingElement($script);

        $this->assertStringContainsString('%2Fhello%2F', $script->getAttribute('src'));
        $this->assertStringNotContainsString('+', $script->getAttribute('src'));
        $this->assertStringNotContainsString('%20', $script->getAttribute('src'));
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
        $this->assertEquals(self::EXPECTED_CACHE_MARKER, $query['cacheMarker']);
    }

    public function testInjectScript() {
        ServiceRequest::setDefaultSerializationMode(ServiceRequest::FORMAT_PATH);

        $script = $this->makeMarkedElement('script');
        $this->head->appendChild($script);

        $this->applyFilter();

        $this->assertMatchingElementExists($script);
        $this->assertHasCompiled('ScriptsProxyService/rewrite-function.js');

        $expectedConfig = $this->config;
        $expectedConfig['whitelist'] = $expectedConfig['match'];
        unset($expectedConfig['match']);
        $expectedConfig['pathInfo'] = true;
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
