<?php

namespace Kibo\Phast\Cache\BlackHole;

class CacheTest extends \PHPUnit\Framework\TestCase {
    private Cache $cache;

    public function setUp(): void {
        parent::setUp();

        $this->cache = new Cache([]);
    }

    public function testCallingWithNoDefault() {
        $shouldBeNull = $this->cache->get('key');
        $shouldBeFive = $this->cache->get('key', function () {
            return 5;
        });
        $shouldBeSix = $this->cache->get('key', function () {
            return 6;
        });

        $this->assertNull($shouldBeNull);
        $this->assertEquals(5, $shouldBeFive);
        $this->assertEquals(6, $shouldBeSix);
    }

    public function testGetDoesNotCache() {
        $callsCount = 0;
        $cb = function () use (&$callsCount) {
            $callsCount++;
            return $callsCount;
        };

        $actual1 = $this->cache->get('key', $cb);
        $actual2 = $this->cache->get('key', $cb);

        $this->assertSame(1, $actual1);
        $this->assertSame(2, $actual2);
        $this->assertSame(2, $callsCount);
    }

    public function testSetDoesNotStore() {
        $this->cache->set('hello', 'world');

        $this->assertNull($this->cache->get('hello'));
    }
}
