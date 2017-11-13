<?php

namespace Kibo\Phast\Retrievers;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\ValueObjects\URL;

class ProxyServiceCacheRetriever implements Retriever {

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var int
     */
    private $urlRefreshTime;

    /**
     * ProxyServiceCacheRetriever constructor.
     *
     * @param Cache $cache
     * @param int $urlRefreshTime
     */
    public function __construct(Cache $cache, $urlRefreshTime) {
        $this->cache = $cache;
        $this->urlRefreshTime = $urlRefreshTime;
    }

    public function retrieve(URL $url) {
        $key = (string)$url . floor(time() / $this->urlRefreshTime);
        $content = $this->cache->get($key);
        if ($content) {
            return $content;
        }
        return false;
    }

    public function getLastModificationTime(URL $url) {
        throw new \RuntimeException('Not implemented');
    }

}
