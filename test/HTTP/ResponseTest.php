<?php

namespace Kibo\Phast\HTTP;


class ResponseTest extends \PHPUnit_Framework_TestCase {

    public function testETagGeneration() {
        $res = new Response();

        $empty = $res->getHeaders()['ETag'];
        $this->assertRegExp('/^[a-f0-9]{32}$/', $empty);
        $this->assertEquals($empty, $res->getHeaders()['ETag']);

        $res->setHeader('Hello', 'World');
        $withHeader = $res->getHeaders()['ETag'];
        $this->assertRegExp('/^[a-f0-9]{32}$/', $withHeader);
        $this->assertNotEquals($empty, $withHeader);

        $res->setContent('Hey!');
        $withContent = $res->getHeaders()['ETag'];
        $this->assertRegExp('/^[a-f0-9]{32}$/', $withContent);
        $this->assertNotEquals($withHeader, $withContent);
    }

}
