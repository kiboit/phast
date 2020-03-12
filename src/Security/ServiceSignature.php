<?php

namespace Kibo\Phast\Security;

use Kibo\Phast\Cache\Cache;

class ServiceSignature {
    const AUTO_TOKEN_SIZE = 128;

    const SIGNATURE_LENGTH = 16;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var array
     */
    private $identities;

    /**
     * ServiceSignature constructor.
     *
     * @param Cache $cache
     */
    public function __construct(Cache $cache) {
        $this->cache = $cache;
    }

    /**
     * @param string|array $identities
     */
    public function setIdentities($identities) {
        if (is_string($identities)) {
            $this->identities = ['' => $identities];
        } else {
            $this->identities = $identities;
        }
    }

    /**
     * @return string
     */
    public function getCacheSalt() {
        $identities = $this->getIdentities();
        return md5(join('=>', array_merge(array_keys($identities), array_values($identities))));
    }

    public function sign($value) {
        $identities = $this->getIdentities();
        $users = array_keys($identities);
        list($user, $token) = [array_shift($users), array_shift($identities)];
        return $user . substr(md5($token . $value), 0, self::SIGNATURE_LENGTH);
    }

    public function verify($signature, $value) {
        $user = substr($signature, 0, -self::SIGNATURE_LENGTH);
        $identities = $this->getIdentities();
        if (!isset($identities[$user])) {
            return false;
        }
        $token = $identities[$user];
        $signer = new self($this->cache);
        $signer->setIdentities([$user => $token]);
        return $signature === $signer->sign($value);
    }

    public static function generateToken() {
        $token = '';
        for ($i = 0; $i < self::AUTO_TOKEN_SIZE; $i++) {
            $token .= chr(mt_rand(33, 126));
        }
        return $token;
    }

    private function getIdentities() {
        if (!isset($this->identities)) {
            $token = $this->cache->get('security-token', function () {
                return self::generateToken();
            });
            $this->identities = ['' => $token];
        }
        return $this->identities;
    }
}
