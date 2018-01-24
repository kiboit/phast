<?php

namespace Kibo\Phast\Filters\TextResources\Composite;


use Kibo\Phast\Filters\TextResources\TextResource;
use Kibo\Phast\Filters\TextResources\TextResourceFilter;
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
        $returned = $this->filter->transform($resource);
        $this->assertSame($resource, $returned);
    }

    public function testApplyingFilters() {
        $resource0 = $this->makeResource();
        $resource1 = $this->makeResource();
        $resource2 = $this->makeResource();
        $filter1 = $this->createMock(TextResourceFilter::class);
        $filter1->expects($this->once())
            ->method('transform')
            ->with($resource0)
            ->willReturn($resource1);
        $filter2 = $this->createMock(TextResourceFilter::class);
        $filter2->expects($this->once())
            ->method('transform')
            ->with($resource1)
            ->willReturn($resource2);

        $this->filter->addFilter($filter1);
        $this->filter->addFilter($filter2);
        $returned = $this->filter->transform($resource0);

        $this->assertSame($resource2, $returned);
    }

    private function makeResource() {
        return new TextResource(URL::fromString('http://phast.test'), 'content');
    }

}
