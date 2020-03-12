<?php

namespace Kibo\Phast\Retrievers;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\ValueObjects\URL;

class CachingRetriever implements Retriever {
    use DynamicCacheSaltTrait {
        getCacheSalt as getDynamicCacheSalt;
    }

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var Retriever
     */
    private $retriever;

    /**
     * CachingRetriever constructor.
     *
     * @param Retriever $retriever
     * @param Cache $cache
     * @param int $defaultCacheTime
     */
    public function __construct(Cache $cache, Retriever $retriever = null, $defaultCacheTime = 0) {
        $this->cache = $cache;
        $this->retriever = $retriever;
    }

    public function retrieve(URL $url) {
        if ($this->retriever) {
            return $this->getCachedWithRetriever($url);
        }
        return $this->getFromCacheOnly($url);
    }

    public function getCacheSalt(URL $url) {
        if ($this->retriever) {
            return $this->retriever->getCacheSalt($url);
        }
        return $this->getDynamicCacheSalt($url);
    }

    private function getCachedWithRetriever(URL $url) {
        return $this->cache->get($this->getCacheKey($url), function () use ($url) {
            return $this->retriever->retrieve($url);
        });
    }

    private function getFromCacheOnly(URL $url) {
        $cached = $this->cache->get($this->getCacheKey($url));
        if (!$cached) {
            return false;
        }
        return $cached;
    }

    private function getCacheKey(URL $url) {
        return $url . '-' . $this->getCacheSalt($url);
    }
}
