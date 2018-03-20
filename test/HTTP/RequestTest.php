<?php

namespace Kibo\Phast\HTTP;

class RequestTest extends \PHPUnit_Framework_TestCase {

    /** @dataProvider pathInfoData */
    public function testPathInfo($env) {
        $req = Request::fromArray([], $env, []);
        $this->assertEquals('/test', $req->getPathInfo());
    }

    public function pathInfoData() {
        yield [['PATH_INFO' => '/test']];
        yield [['PHP_SELF' => '/phast.php', 'DOCUMENT_URI' => '/phast.php/test']];
    }

}
