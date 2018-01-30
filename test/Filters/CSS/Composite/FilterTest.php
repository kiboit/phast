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
        $this->filter = new Filter();
    }

    public function testReturnSameResourceWhenEmpty() {
        $resource = $this->makeResource();
        $returned = $this->filter->apply($resource, []);
        $this->assertSame($resource, $returned);
    }

    public function testApplyingFilters() {
        $resource0 = $this->makeResource();
        $resource1 = $this->makeResource();
        $resource2 = $this->makeResource();
        $filter1 = $this->createMock(ServiceFilter::class);
        $filter1->expects($this->once())
            ->method('apply')
            ->with($resource0, [])
            ->willReturn($resource1, []);
        $filter2 = $this->createMock(ServiceFilter::class);
        $filter2->expects($this->once())
            ->method('apply')
            ->with($resource1, [])
            ->willReturn($resource2);

        $this->filter->addFilter($filter1);
        $this->filter->addFilter($filter2);
        $returned = $this->filter->apply($resource0, []);

        $this->assertSame($resource2, $returned);
    }

    public function testGetCacheHash() {
        $hashes = [];
        $resource = Resource::makeWithContent(URL::fromString('http://phast.test'), 'the-content');
        $hashes[] = $this->filter->getCacheHash($resource, []);
        $this->filter->addFilter($this->createMock(ServiceFilter::class));
        $hashes[] = $this->filter->getCacheHash($resource, []);
        $this->filter->addFilter($this->createMock(ServiceFilter::class));
        $hashes[] = $this->filter->getCacheHash($resource, []);
        $resource2 = Resource::makeWithContent(URL::fromString('http://phast.test'), 'other-content');
        $hashes[] = $this->filter->getCacheHash($resource2, []);

        foreach ($hashes as $idx => $hash) {
            $this->assertTrue(is_string($hash), "Hash $idx is not string");
            $this->assertNotEmpty($hash, "Hash $idx is an empty string");
        }
        $this->assertEquals($hashes, array_unique($hashes), "Hashed has duplicates");
    }

    private function makeResource() {
        return Resource::makeWithContent(URL::fromString('http://phast.test'), 'content');
    }

}
