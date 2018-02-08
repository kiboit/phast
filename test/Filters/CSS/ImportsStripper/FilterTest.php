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
        $resource = Resource::makeWithContent(URL::fromString('http://phast.test'), $this->css);
        $result = (new Filter())->apply($resource, ['strip-imports' => 1])->getContent();

        $this->assertNotContains('@import \'custom.css\';', $result);
        $this->assertNotContains('@import url("chrome://communicator/skin/");', $result);
        $this->assertContains('@import url("bluish.css") projection, tv', $result);
        $this->assertContains('@import "common.css" screen, projection;', $result);
        $this->assertContains("@import url('landscape.css') screen and (orientation:landscape)", $result);
    }

    public function testNotStrippingWhenNotRequired() {
        $resource = Resource::makeWithContent(URL::fromString('http://phast.test'), $this->css);
        $result = (new Filter())->apply($resource, [])->getContent();
        $this->assertEquals($this->css, $result);
    }

}
