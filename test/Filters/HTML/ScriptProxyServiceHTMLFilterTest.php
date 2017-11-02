<?php

namespace Kibo\Phast\Filters\HTML;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\ValueObjects\URL;

class ScriptProxyServiceHTMLFilterTest extends HTMLFilterTestCase {

    public function testRewrite() {

        $urls = [
            'http://example.com/script.js',
            'http://test.com/script.js',
            'http://example.com/script1.cs',
            'http://norewrite.com/script.js',
            'http://local.domain/script.js',
            '/norewrite.js'
        ];

        $rewrite1 = $this->dom->createElement('script');
        $rewrite1->setAttribute('type', 'application/javascript');
        $rewrite1->setAttribute('src', $urls[0]);
        $rewrite2 = $this->dom->createElement('script');
        $rewrite2->setAttribute('src', $urls[1]);
        $noRewrite1 = $this->dom->createElement('script');
        $noRewrite2 = $this->dom->createElement('script');
        $noRewrite2->setAttribute('src', $urls[2]);
        $noRewrite2->setAttribute('type', 'application/coffeescript');
        $noRewrite3 = $this->dom->createElement('script');
        $noRewrite3->setAttribute('src', $urls[3]);
        $noRewrite4 = $this->dom->createElement('script');
        $noRewrite4->setAttribute('src', $urls[4]);
        $noRewrite5 = $this->dom->createElement('script');
        $noRewrite5->setAttribute('src', $urls[5]);

        $this->head->appendChild($rewrite1);
        $this->head->appendChild($rewrite2);
        $this->head->appendChild($noRewrite1);
        $this->head->appendChild($noRewrite2);
        $this->head->appendChild($noRewrite3);
        $this->head->appendChild($noRewrite4);
        $this->head->appendChild($noRewrite5);

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
        $filter = new ScriptProxyServiceHTMLFilter(
            URL::fromString('http://local.domain/index.php'),
            $config,
            new ServiceSignature($this->createMock(Cache::class)),
            $functions
        );
        $filter->transformHTMLDOM($this->dom);

        foreach ([$rewrite1->getAttribute('src'), $rewrite2->getAttribute('src')] as $i => $src) {
            $url = parse_url($src);
            $this->assertEquals($config['serviceUrl'], $url['path']);
            $query = [];
            parse_str($url['query'], $query);
            $this->assertArrayHasKey('src', $query);
            $this->assertArrayHasKey('cacheMarker', $query);
            $this->assertArrayHasKey('token', $query);
            $this->assertEquals($urls[$i], $query['src']);
            $this->assertEquals(2, $query['cacheMarker']);
            $this->assertNotEmpty($query['token']);
        }

        $this->assertEquals($urls[2], $noRewrite2->getAttribute('src'));
        $this->assertEquals($urls[3], $noRewrite3->getAttribute('src'));
        $this->assertEquals($urls[4], $noRewrite4->getAttribute('src'));
        $this->assertEquals($urls[5], $noRewrite5->getAttribute('src'));
    }

}
