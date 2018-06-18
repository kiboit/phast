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
        yield [['PHP_SELF' => '/phast.php', 'REQUEST_URI' => '/phast.php/test']];
        yield [['PHP_SELF' => '/phast.php', 'REQUEST_URI' => '/phast.php/test?q=v']];
        yield [['PHP_SELF' => '/index.php', 'REQUEST_URI' => '/test']];
        yield [['PHP_SELF' => '/index.php', 'DOCUMENT_URI' => '/test']];
    }

    /** @dataProvider documentRootData */
    public function testDocumentRoot($env, $expectedDocumentRoot) {
        $req = Request::fromArray([], $env, []);
        $this->assertEquals($expectedDocumentRoot, $req->getDocumentRoot());
    }

    public function documentRootData() {
        yield [
            [
                'DOCUMENT_ROOT' => '/var/www'
            ],
            '/var/www'
        ];

        yield [
            [
                'DOCUMENT_ROOT' => '/var/www',
                'SCRIPT_FILENAME' => '/var/www/subdomain/index.php',
                'SCRIPT_NAME' => '/index.php'
            ],
            '/var/www/subdomain'
        ];

        yield [
            [
                'DOCUMENT_ROOT' => '/var/www',
                'SCRIPT_FILENAME' => '/var/www/subdomain/index.php',
                'SCRIPT_NAME' => '/mismatch.php'
            ],
            '/var/www'
        ];

        yield [
            [
                'DOCUMENT_ROOT' => 'C:\\var\\www',
                'SCRIPT_FILENAME' => 'C:\\var\\www\\subdomain\\index.php',
                'SCRIPT_NAME' => '/index.php'
            ],
            'C:/var/www/subdomain',
        ];
    }

}
