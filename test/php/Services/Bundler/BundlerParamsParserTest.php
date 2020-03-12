<?php

namespace Kibo\Phast\Services\Bundler;

use Kibo\Phast\PhastTestCase;
use Kibo\Phast\Services\ServiceRequest;

class BundlerParamsParserTest extends PhastTestCase {
    public function testGroupsParsing() {
        $params = ['a_0' => 'a0', 'a_1' => 'a1', 't_0' => 't0', 't_1' => 't1', 'ignored' => 'none'];
        $request = (new ServiceRequest())->withParams($params);

        $expected = [['a' => 'a0', 't' => 't0'], ['a' => 'a1', 't' => 't1']];
        $actual = (new BundlerParamsParser())->parse($request);
        $this->assertEquals($expected, $actual);
    }
}
