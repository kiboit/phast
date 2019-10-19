<?php
namespace Kibo\Phast\Services\Bundler;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\Common\Base64url;

class TokenRefMaker {

    private $cache;

    public function __construct(Cache $cache) {
        $this->cache = $cache;
    }

    public function getRef($token, array $params) {
        $cachedParams = $this->cache->get($token);
        if (!$cachedParams) {
            $this->cache->set($token, $params);
            $cachedParams = $this->cache->get($token);
        }
        if ($cachedParams === $params) {
            return Base64url::encode(hex2bin($token));
        }
    }

    public function getParams($ref) {
        return $this->cache->get(bin2hex(Base64url::decode($ref)));
    }

}
