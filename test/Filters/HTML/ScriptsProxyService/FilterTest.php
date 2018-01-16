<?php

namespace Kibo\Phast\Filters\HTML\ScriptsProxyService;

use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Filters\HTML\HTMLFilterTestCase;
use Kibo\Phast\ValueObjects\URL;

class FilterTest extends HTMLFilterTestCase {

    public function testRewrite() {
        $urls = [
            'http://example.com/script.js',
            'http://test.com/script.js',
            'http://local.domain/rewrite.js',
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

        $this->runFilter();

        foreach ([$rewrite1->getAttribute('src'), $rewrite2->getAttribute('src'), $rewrite3->getAttribute('src')] as $i => $src) {
            $url = parse_url($src);
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

    public function testInjectScript() {
        $script = $this->dom->createElement('script');
        $this->head->appendChild($script);

        $this->runFilter();

        $scripts = iterator_to_array($this->dom->getElementsByTagName('script'));
        $this->assertEquals(2, sizeof($scripts));
        $this->assertSame($script, $scripts[1]);
        $this->assertContains('script-proxy.php', $scripts[0]->textContent);
    }

    public function testDontInjectScriptForNothing() {
        $this->runFilter();

        $scripts = iterator_to_array($this->dom->getElementsByTagName('script'));
        $this->assertEquals(0, sizeof($scripts));
    }

    public function testDontInjectScriptForNonJS() {
        $script = $this->dom->createElement('script');
        $script->setAttribute('type', 'nonsense');
        $this->head->appendChild($script);

        $this->runFilter();

        $scripts = iterator_to_array($this->dom->getElementsByTagName('script'));
        $this->assertEquals(1, sizeof($scripts));
        $this->assertSame($script, $scripts[0]);
    }

    private function runFilter() {
        $config = [
            'match' => [
                '/example\.com/',
                '/test\.com/'
            ],
            'serviceUrl' => 'script-proxy.php',
            'urlRefreshTime' => 7200
        ];
        $functions = new ObjectifiedFunctions();
        $functions->time = function () use ($config) {
            return $config['urlRefreshTime'] * 2.5;
        };
        $filter = new Filter(
            URL::fromString('http://local.domain/index.php'),
            $config,
            $functions
        );
        $filter->transformHTMLDOM($this->dom);
    }

}
