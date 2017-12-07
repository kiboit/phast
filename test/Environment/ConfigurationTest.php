<?php

namespace Kibo\Phast\Environment;

use Kibo\Phast\Services\ServiceRequest;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase {

    private $configTemplate = [
        'documents' => [
            'filters' => [
                'filter1' => [],
                'filter2' => [],
                'filter3' => []
            ]
        ],

        'images' => [
            'filters' => [
                'filter1' => [],
                'filter2' => [],
                'filter3' => []
            ]
        ],

        'logging' => [
            'logWriters' => [
                'writer1' => [],
                'writer2' => [],
                'writer3' => []
            ]
        ],

        'switches' => [
            'phast' => true
        ]
    ];

    public function testRemovingDisabledItems() {
        $config = $this->configTemplate;
        $config['documents']['filters']['filter2']['enabled'] = false;
        $config['images']['filters']['filter1']['enabled'] = false;
        $config['images']['filters']['filter2']['enabled'] = true;
        $config['images']['filters']['filter3']['enabled'] = false;
        $config['logging']['logWriters']['writer1']['enabled'] = false;

        $actual = (new Configuration($config))->toArray();
        unset ($config['documents']['filters']['filter2']);
        unset ($config['images']['filters']['filter1']);
        unset ($config['images']['filters']['filter3']);
        unset ($config['logging']['logWriters']['writer1']);

        $this->assertEquals($config, $actual);
    }

    public function testRemovingSwitchedOffItems() {
        $config = $this->configTemplate;
        $config['documents']['filters']['filter1']['enabled'] = 's1';
        $config['documents']['filters']['filter2']['enabled'] = 's2';
        $config['images']['filters']['filter3']['enabled'] = 's1';
        $config['logging']['logWriters']['writer1']['enabled'] = 's1';
        $config['logging']['logWriters']['writer2']['enabled'] = 'undefined';

        $config['switches']['s1'] = false;
        $config['switches']['s2'] = true;

        $actual = (new Configuration($config))->toArray();
        unset ($config['documents']['filters']['filter1']);
        unset ($config['images']['filters']['filter3']);
        unset ($config['logging']['logWriters']['writer1']);

        $this->assertEquals($config, $actual);
    }

    public function testMergingSwitchesFromRequest() {
        $config = $this->configTemplate;
        $config['documents']['filters']['filter1']['enabled'] = 's';
        $config['documents']['filters']['filter2']['enabled'] = 'm1';
        $config['documents']['filters']['filter3']['enabled'] = 'm2';
        $config['images']['filters']['filter1']['enabled'] = 'r';
        $config['switches'] = ['s' => false, 'm1' => true, 'm2' => false, 'phast' => true];
        $request = $this->createMock(ServiceRequest::class);
        $request->method('getSwitches')
            ->willReturn(['m1' => false, 'm2' => true, 'r' => false]);

        $actual = (new Configuration($config))->withServiceRequest($request)->toArray();
        unset ($config['documents']['filters']['filter1']);
        unset ($config['documents']['filters']['filter2']);
        unset ($config['images']['filters']['filter1']);

        $config['switches']['s'] = false;
        $config['switches']['m1'] = false;
        $config['switches']['m2'] = true;
        $config['switches']['r'] = false;

        $this->assertEquals($config, $actual);
    }

    public function testDefaultPhastSwitch() {
        $config = $this->configTemplate;
        unset ($config['switches']);
        $actual = (new Configuration($config))->toArray();
        $this->assertArrayHasKey('switches', $actual);
        $this->assertArrayHasKey('phast', $actual['switches']);
        $this->assertTrue($actual['switches']['phast']);
    }
}
