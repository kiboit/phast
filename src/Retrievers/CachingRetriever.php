<?php

namespace Kibo\Phast\Retrievers;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\ValueObjects\URL;

class CachingRetriever implements Retriever {

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var Retriever
     */
    private $retriever;

    /**
     * @var int
     */
    private $defaultCacheTime;

    /**
     * CachingRetriever constructor.
     *
     * @param Retriever $retriever
     * @param Cache $cache
     * @param int $defaultCacheTime
     */
    public function __construct(Cache $cache, Retriever $retriever = null, $defaultCacheTime = 0) {
        $this->retriever = $retriever;
        $this->cache = $cache;
        $this->defaultCacheTime = $defaultCacheTime;
    }

    public function retrieve(URL $url) {
        if ($this->retriever) {
            return $this->getCachedWithRetriever($url);
        }
        return $this->getFromCacheOnly($url);
    }

    public function getCacheSalt(URL $url) {
        return false;
    }

    private function getCachedWithRetriever(URL $url) {
        return $this->cache->get((string)$url, function () use ($url) {
            return $this->retriever->retrieve($url);
        }, $this->defaultCacheTime);
    }

    private function getFromCacheOnly(URL $url) {
        $cached = $this->cache->get((string)$url);
        if (!$cached) {
            return false;
        }
        return $cached;
    }

}
