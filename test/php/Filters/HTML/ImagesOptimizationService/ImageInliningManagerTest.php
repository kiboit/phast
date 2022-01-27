<?php

namespace Kibo\Phast\Filters\HTML\ImagesOptimizationService;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\PhastTestCase;
use Kibo\Phast\ValueObjects\Resource;
use Kibo\Phast\ValueObjects\URL;

class ImageInliningManagerTest extends PhastTestCase {
    const MAX_INLINING_SIZE = 512;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $cache;

    /**
     * @var ImageInliningManager
     */
    private $manager;

    public function setUp(): void {
        $this->cache = $this->createMock(Cache::class);
        $this->manager = new ImageInliningManager($this->cache, self::MAX_INLINING_SIZE);
    }

    /**
     * @dataProvider maybeStoreForInliningData
     */
    public function testMaybeStoreForInlining($mimeType, $size = self::MAX_INLINING_SIZE, $shouldStore = true) {
        $url = URL::fromString(self::BASE_URL);
        $content = str_repeat('a', $size);
        $expectation = $shouldStore ? $this->once() : $this->never();
        $resource = Resource::makeWithContent($url, $content, $mimeType);


        $usedKey = $cachedValue = null;
        $this->cache->expects($expectation)
            ->method('set')
            ->willReturnCallback(function ($key, $value) use (&$usedKey, &$cachedValue) {
                $usedKey = $key;
                $cachedValue = $value;
            });
        $this->manager->maybeStoreForInlining($resource);

        if ($shouldStore) {
            $this->assertEquals($this->getExpectedCacheKey($resource), $usedKey);
            $this->assertEquals($resource->toDataURL(), $cachedValue);
        }
    }

    public function maybeStoreForInliningData() {
        return [
            ['image/jpeg'],
            ['image/gif'],
            ['image/png'],
            ['image/svg+xml'],
            ['image/webp', self::MAX_INLINING_SIZE, false],
            ['image/jpeg', self::MAX_INLINING_SIZE + 1, false],
        ];
    }

    /**
     * @dataProvider getUrlForInliningWithBinaryFormatsData
     */
    public function testGetUrlForInliningWithBinaryFormats($mimeType) {
        $resource = Resource::makeWithContent(URL::fromString(self::BASE_URL), 'the-content', $mimeType);
        $expected = 'returned-value';
        $this->cache->expects($this->once())
            ->method('get')
            ->with($this->getExpectedCacheKey($resource))
            ->willReturn($expected);
        $actual = $this->manager->getUrlForInlining($resource);
        $this->assertEquals($expected, $actual);
    }

    public function getUrlForInliningWithBinaryFormatsData() {
        return [
            ['image/jpeg'],
            ['image/gif'],
            ['image/png'],
        ];
    }

    public function testGetUrlForInliningWithSVG() {
        $url = URL::fromString(self::BASE_URL);
        $mimeType = 'image/svg+xml';
        $small = Resource::makeWithContent($url, 'a', $mimeType);
        $big = Resource::makeWithContent($url, str_repeat('a', self::MAX_INLINING_SIZE + 1), $mimeType);
        $this->cache->expects($this->never())->method('get');

        $this->assertNull($this->manager->getUrlForInlining($big));
        $this->assertEquals($small->toDataURL(), $this->manager->getUrlForInlining($small));
    }

    private function getExpectedCacheKey(Resource $resource) {
        return $resource->getUrl()->toString()
            . '|' . $resource->getCacheSalt()
            . '|' . self::MAX_INLINING_SIZE;
    }
}
