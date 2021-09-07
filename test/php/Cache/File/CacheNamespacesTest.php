<?php
namespace Kibo\Phast\Cache\File;

class CacheNamespacesTest extends CacheTestCase {
    public function testCache() {
        $config = array_replace_recursive($this->config, [
            'diskCleanup' => [
                'maxSize' => 0,
                'portionToFree' => 1,
                'keepNamespaces' => ['a'],
            ],
        ]);

        $a = new Cache($config, 'a', $this->functions);
        $b = new Cache($config, 'b', $this->functions);

        $returnTest = function () {
            return 'test';
        };

        $returnNew = function () {
            return 'new';
        };

        $this->assertEquals('test', $a->get('test', $returnTest));
        $this->assertEquals('test', $b->get('test', $returnTest));

        $a->getDiskCleanup()->forceExecution();

        $this->assertEquals('test', $a->get('test', $returnNew));
        $this->assertEquals('new', $b->get('test', $returnNew));
    }
}
