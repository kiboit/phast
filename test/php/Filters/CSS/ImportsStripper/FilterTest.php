<?php

namespace Kibo\Phast\Filters\CSS\ImportsStripper;

use Kibo\Phast\ValueObjects\Resource;
use Kibo\Phast\ValueObjects\URL;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase {
    private $css = '
@import url("fineprint.css") print
;
@import url("bluish.css") projection, tv;
@import \'custom.css\';
@import url("chrome://communicator/skin/");
@import "common.css" screen, projection;
@import url(\'landscape.css\') screen and (orientation:landscape);

selector { rule: value }
';

    public function testStrippingImports() {
        $result = (new Filter())
            ->apply($this->makeResource(), ['strip-imports' => 1])
            ->getContent();

        $this->assertNotContains('@import \'custom.css\';', $result);
        $this->assertNotContains('@import url("chrome://communicator/skin/");', $result);
        $this->assertContains('@import url("bluish.css") projection, tv', $result);
        $this->assertContains('@import "common.css" screen, projection;', $result);
        $this->assertContains("@import url('landscape.css') screen and (orientation:landscape)", $result);
    }

    public function testNotStrippingWhenNotRequired() {
        $result = (new Filter())
            ->apply($this->makeResource(), [])
            ->getContent();
        $this->assertEquals($this->css, $result);
    }

    public function testCacheSalt() {
        $filter = new Filter();
        $resource = $this->makeResource();
        $this->assertNotEquals(
            $filter->getCacheSalt($resource, []),
            $filter->getCacheSalt($resource, ['strip-imports' => 1])
        );
    }

    private function makeResource() {
        return Resource::makeWithContent(URL::fromString('http://phast.test'), $this->css);
    }
}
