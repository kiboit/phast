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
                'filter3' => [],
            ],
        ],

        'images' => [

            'enable-cache' => true,

            'filters' => [
                'filter1' => [
                    'item1',
                    'item2',
                ],
                'filter2' => [],
                'filter3' => [],
            ],
        ],

        'logging' => [
            'logWriters' => [
                'writer1' => [],
                'writer2' => [],
                'writer3' => [],
            ],
        ],

        'styles' => [
            'filters' => [],
        ],

        'switches' => [
            'phast' => true,
            'diagnostics' => false,
        ],
    ];

    public function testToArray() {
        $config = $this->configTemplate;
        $config['images']['filters']['filter2']['enabled'] = 'diagnostics';
        $actual = (new Configuration($config))->toArray();
        $this->assertEquals($config, $actual);
    }

    public function testMergingDefaultConfigWithUserConfig() {
        $userConfig = [
            'documents' => [
                'filters' => [
                    'filter1' => [
                        'setting' => 'value',
                    ],
                ],
            ],
            'images' => [
                'enable-cache' => false,
                'filters' => [
                    'filter1' => [
                        'item3',
                    ],
                ],
            ],
            'switches' => [
                'diagnostics' => true,
                'aux' => true,
            ],
        ];
        $expected = $this->configTemplate;
        $expected['documents']['filters']['filter1']['setting'] = 'value';
        $expected['images']['filters']['filter1'][] = 'item3';
        $expected['images']['enable-cache'] = false;
        $expected['switches']['diagnostics'] = true;
        $expected['switches']['aux'] = true;

        $default = new Configuration($this->configTemplate);
        $user = new Configuration($userConfig);
        $actual = $default->withUserConfiguration($user)->toArray();

        $this->assertEquals($expected, $actual);
    }

    public function testRemovingDisabledItems() {
        $config = $this->configTemplate;
        $config['documents']['filters']['filter2']['enabled'] = false;
        $config['images']['filters']['filter1']['enabled'] = false;
        $config['images']['filters']['filter2']['enabled'] = true;
        $config['images']['filters']['filter3']['enabled'] = false;
        $config['logging']['logWriters']['writer1']['enabled'] = false;

        $actual = (new Configuration($config))->getRuntimeConfig()->toArray();
        unset($config['documents']['filters']['filter2']);
        unset($config['images']['filters']['filter1']);
        unset($config['images']['filters']['filter3']);
        unset($config['logging']['logWriters']['writer1']);

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

        $actual = (new Configuration($config))->getRuntimeConfig()->toArray();
        unset($config['documents']['filters']['filter1']);
        unset($config['images']['filters']['filter3']);
        unset($config['logging']['logWriters']['writer1']);

        $this->assertEquals($config, $actual);
    }

    public function testMergingSwitchesFromRequest() {
        $config = $this->configTemplate;
        $config['documents']['filters']['filter1']['enabled'] = 's';
        $config['documents']['filters']['filter2']['enabled'] = 'm1';
        $config['documents']['filters']['filter3']['enabled'] = 'm2';
        $config['images']['filters']['filter1']['enabled'] = 'r';
        $config['switches'] = ['s' => false, 'm1' => true, 'm2' => false, 'phast' => true, 'diagnostics' => false];
        $request = $this->createMock(ServiceRequest::class);
        $request->method('getSwitches')
            ->willReturn(Switches::fromArray(['m1' => false, 'm2' => true, 'r' => false]));

        $actual = (new Configuration($config))
            ->withServiceRequest($request)
            ->getRuntimeConfig()
            ->toArray();
        unset($config['documents']['filters']['filter1']);
        unset($config['documents']['filters']['filter2']);
        unset($config['images']['filters']['filter1']);

        $config['switches']['s'] = false;
        $config['switches']['m1'] = false;
        $config['switches']['m2'] = true;
        $config['switches']['r'] = false;

        $this->assertEquals($config, $actual);
    }

    public function testDefaultPhastSwitch() {
        $config = $this->configTemplate;
        unset($config['switches']);
        $actual = (new Configuration($config))->getRuntimeConfig()->toArray();
        $this->assertArrayHasKey('switches', $actual);
        $this->assertArrayHasKey('phast', $actual['switches']);
        $this->assertTrue($actual['switches']['phast']);
    }

    public function testImageCacheSetting() {
        $config = $this->configTemplate;
        $actual = (new Configuration($config))->toArray();
        $this->assertTrue($actual['images']['enable-cache']);

        $config['images']['enable-cache'] = false;
        $actual = (new Configuration($config))->toArray();
        $this->assertFalse($actual['images']['enable-cache']);

        $config['images']['enable-cache'] = 's1';
        $config['switches']['s1'] = false;
        $actual = (new Configuration($config))->getRuntimeConfig()->toArray();
        $this->assertFalse($actual['images']['enable-cache']);

        $config['switches']['s1'] = true;
        $actual = (new Configuration($config))->getRuntimeConfig()->toArray();
        $this->assertTrue($actual['images']['enable-cache']);
    }
}
