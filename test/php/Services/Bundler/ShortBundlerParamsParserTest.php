<?php

namespace Kibo\Phast\Services\Bundler;

use Kibo\Phast\HTTP\Request;
use Kibo\Phast\PhastTestCase;
use Kibo\Phast\Services\ServiceRequest;

class ShortBundlerParamsParserTest extends PhastTestCase {
    /**
     * @var ShortBundlerParamsParser
     */
    private $parser;

    public function setUp(): void {
        parent::setUp();
        $this->parser = new BundlerParamsParser();
    }

    public function testParamsMapping() {
        $expected = [
            [
                'src' => 'the://source',
                'strip-imports' => '1',
                'cacheMarker' => 'the-cache-marker',
                'token' => 'the-token',
                'isScript' => '1',
                'not-mapped' => 'original',
            ],
        ];
        $query = 's=00the%3A%2F%2Fsource&i&c=the-cache-marker&t=the-token&not-mapped=original&j';
        $this->assertEquals($expected, $this->parseString($query));
        $this->assertEquals($expected, $this->parseString(str_rot13($query)));
        $this->assertEquals($expected, $this->parseString(
            preg_replace_callback('/(%..)|([A-Z])/i', function ($match) {
                return $match[1] ? $match[1] : str_rot13($match[2]);
            }, $query)
        ));
    }

    public function testParamsGrouping() {
        $expected = [
            [
                'src' => 's1',
                'token' => 't1',
                'not-mapped' => 'o1',
                'cacheMarker' => 'cm1',
            ],
            [
                'src' => 's2',
                'token' => 't2',
                'not-mapped' => 'o2',
            ],
            [
                'src' => 's3',
                'cacheMarker' => 'cm3',
            ],
        ];
        $query = 'service=bundler';
        $query .= '&s=00s1&t=t1&not-mapped=o1&c=cm1';
        $query .= '&s=012&t=t2&not-mapped=o2';
        $query .= '&s=013&c=cm3';

        $actual = $this->parseString($query);
        $this->assertEquals($expected, $actual);
    }

    public function testSrcPrefixing() {
        $expected = [
            ['src' => '/this/is/the/root/and/this/is/specific'],
            ['src' => '/this/is/the/root/but/these/are/others'],
            ['src' => '/this/is/the/root/but/this-is-third'],
            ['src' => '/another/root/smaller'],
            ['src' => '/another/root/somewhat-bigger'],
            ['src' => str_repeat('a', 1296) . '/1'],
            ['src' => str_repeat('a', 1296) . '/2'],
        ];
        $query = 's=00/this/is/the/root/and/this/is/specific';
        $query .= '&s=0ibut/these/are/others';
        $query .= '&s=0ois-is-third';
        $query .= '&s=00/another/root/smaller';
        $query .= '&s=0fomewhat-bigger';
        $query .= '&s=00' . str_repeat('a', 1296) . '/1';
        $query .= '&s=zza/2';

        $actual = $this->parseString($query);
        $this->assertEquals($expected, $actual);
    }

    private function parseString($string) {
        $httpRequest = Request::fromArray([], ['REQUEST_URI' => "/?$string"]);
        $serviceRequest = ServiceRequest::fromHTTPRequest($httpRequest);
        return (new ShortBundlerParamsParser())->parse($serviceRequest);
    }
}
