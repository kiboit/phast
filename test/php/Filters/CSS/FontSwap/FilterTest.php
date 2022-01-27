<?php

namespace Kibo\Phast\Filters\CSS\FontSwap;

use Kibo\Phast\PhastTestCase;
use Kibo\Phast\ValueObjects\Resource;
use Kibo\Phast\ValueObjects\URL;

class FilterTest extends PhastTestCase {
    /**
     * @var Filter
     */
    private $filter;

    public function setUp(): void {
        parent::setUp();
        $this->filter = new Filter();
    }

    public function testAddingSwap() {
        $css = "@font-face {}\n@font-face{ font-family: \"Something\";} .else{color: red;}";
        $expected = "@font-face {font-display:swap;}\n@font-face{font-display:swap; font-family: \"Something\";} .else{color: red;}";
        $resource = Resource::makeWithContent(URL::fromString(self::BASE_URL), $css);
        $actual = $this->filter->apply($resource, []);
        $this->assertEquals($expected, $actual->getContent());
    }

    public function swapExceptionsData() {
        yield ['f o n t a w e s o m e', false];
        yield ['times new roman', true];
        yield ['GeNeRaTePrEsS', false];
    }

    /** @dataProvider swapExceptionsData */
    public function testSwapExceptions($fontFamily, $shouldSwap) {
        $css = "@font-face{font-family:\"$fontFamily\"}";
        $resource = Resource::makeWithContent(URL::fromString(self::BASE_URL), $css);
        $actual = $this->filter->apply($resource, [])->getContent();
        if ($shouldSwap) {
            $this->assertStringContainsString('font-display:swap', $actual);
        } else {
            $this->assertStringContainsString('font-display:block', $actual);
        }
    }
}
