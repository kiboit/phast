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

    public function setUp() {
        parent::setUp();
        $this->parser = new BundlerParamsParser();
    }

    public function testParamsMapping() {
        $expected = [
            [
                'src' => 'the-source',
                'strip-imports' => '1',
                'cacheMarker' => 'the-cache-marker',
                'token' => 'the-token',
                'not-mapped' => 'original'
            ]
        ];
        $query = 's=the-source&i&c=the-cache-marker&t=the-token&not-mapped=original';
        $actual = $this->parseString($query);
        $this->assertEquals($expected, $actual);
    }

    public function testParamsGrouping() {
        $expected = [
            [
                'src' => 's1',
                'token' => 't1',
                'not-mapped' => 'o1',
                'cacheMarker' => 'cm1'
            ],
            [
                'src' => 's2',
                'token' => 't2',
                'not-mapped' => 'o2'
            ],
            [
                'src' => 's3',
                'cacheMarker' => 'cm3'
            ]
        ];
        $query = 's=s1&t=t1&not-mapped=o1&c=cm1';
        $query .= '&s=s2&t=t2&not-mapped=o2';
        $query .= '&s=s3&c=cm3';

        $actual = $this->parseString($query);
        $this->assertEquals($expected, $actual);
    }

    private function parseString($string) {
        $httpRequest = Request::fromArray([], ['QUERY_STRING' => $string]);
        $serviceRequest = ServiceRequest::fromHTTPRequest($httpRequest);
        return (new ShortBundlerParamsParser())->parse($serviceRequest);
    }

}
