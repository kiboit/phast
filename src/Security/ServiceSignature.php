<?php

namespace Kibo\Phast\Security;

use Kibo\Phast\Cache\Cache;

class ServiceSignature {

    const AUTO_TOKEN_SIZE = 128;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var string
     */
    private $securityToken;

    /**
     * ServiceSignature constructor.
     *
     * @param Cache $cache
     */
    public function __construct(Cache $cache) {
        $this->cache = $cache;
    }

    /**
     * @param string $securityToken
     */
    public function setSecurityToken($securityToken) {
        $this->securityToken = $securityToken;
    }

    public function sign($value) {
        return substr(base_convert(md5($this->getSecurityToken() . $value), 16, 36), 0, 16);
    }

    public function verify($signature, $value) {
        return $signature === $this->sign($value);
    }

    private function getSecurityToken() {
        if (!isset ($this->securityToken)) {
            $this->securityToken = $this->cache->get('security-token', function () {
                return $this->generateToken();
            });
        }
        return $this->securityToken;
    }

    private function generateToken() {
        $token = '';
        for ($i = 0; $i < self::AUTO_TOKEN_SIZE; $i++) {
            $token .= chr(mt_rand(33, 126));
        }
        return $token;
    }

}
