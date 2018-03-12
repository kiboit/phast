<?php

namespace Kibo\Phast\Filters\CSS\Composite;


use Kibo\Phast\Services\ServiceFilter;
use Kibo\Phast\ValueObjects\Resource;
use Kibo\Phast\ValueObjects\URL;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase {

    /**
     * @var Filter
     */
    private $filter;

    public function setUp() {
        parent::setUp();
        $this->filter = new Filter('service-url');
    }

    public function testGetCacheHash() {
        $resource = Resource::makeWithContent(URL::fromString('http://phast.test'), 'the-content');

        $this->assertNotEquals(
            $this->filter->getCacheHash($resource, []),
            (new Filter('new-service-url'))->getCacheHash($resource, []),
            'Cache hash does not take service url into account'
        );

        $hashes = [];
        $hashes[] = $this->filter->getCacheHash($resource, []);
        $this->filter->addFilter($this->createMock(ServiceFilter::class));
        $hashes[] = $this->filter->getCacheHash($resource, []);
        $this->filter->addFilter($this->createMock(ServiceFilter::class));
        $hashes[] = $this->filter->getCacheHash($resource, []);
        $resource2 = Resource::makeWithContent(URL::fromString('http://phast.test'), 'other-content');
        $hashes[] = $this->filter->getCacheHash($resource2, []);
        $resource3 = Resource::makeWithContent(URL::fromString('http://phast.test/other-url.css'), 'the-content');
        $hashes[] = $this->filter->getCacheHash($resource3, []);

        foreach ($hashes as $idx => $hash) {
            $this->assertTrue(is_string($hash), "Hash $idx is not string");
            $this->assertNotEmpty($hash, "Hash $idx is an empty string");
        }
        $this->assertEquals($hashes, array_unique($hashes), "Hashed has duplicates");
    }

    public function testRemoveComments() {
        $css = '/* a comment here */ selector {rule: /* comment in a weird place*/ value}';
        $resource = Resource::makeWithContent(URL::fromString('http://phast.test'), $css);
        $filtered = $this->filter->apply($resource, []);
        $this->assertEquals(' selector {rule:  value}', $filtered->getContent());
    }

}
