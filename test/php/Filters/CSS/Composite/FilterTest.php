<?php

namespace Kibo\Phast\Filters\CSS\Composite;

use Kibo\Phast\ValueObjects\Resource;
use Kibo\Phast\ValueObjects\URL;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase {
    /**
     * @var Filter
     */
    private $filter;

    public function setUp(): void {
        parent::setUp();
        $this->filter = new Filter();
    }

    public function testRemoveComments() {
        $css = '/* a comment here */ selector {rule: /* comment in a weird place*/ value}';
        $resource = Resource::makeWithContent(URL::fromString('http://phast.test'), $css);
        $filtered = $this->filter->apply($resource, []);
        $this->assertEquals(' selector {rule:  value}', $filtered->getContent());
    }
}
