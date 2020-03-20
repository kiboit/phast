<?php
namespace Kibo\Phast\Services\Bundler;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\Common\Base64url;
use Kibo\Phast\Common\JSON;

class TokenRefMaker {
    private $cache;

    public function __construct(Cache $cache) {
        $this->cache = $cache;
    }

    public function getRef($token, array $params) {
        $ref = Base64url::shortHash(JSON::encode($params));
        $cachedParams = $this->cache->get($ref);
        if (!$cachedParams) {
            $this->cache->set($ref, $params);
            $cachedParams = $this->cache->get($ref);
        }
        if ($cachedParams === $params) {
            return $ref;
        }
    }

    public function getParams($ref) {
        return $this->cache->get($ref);
    }
}
