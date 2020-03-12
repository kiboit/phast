<?php

namespace Kibo\Phast\Services\Bundler;

use Kibo\Phast\Common\JSON;
use Kibo\Phast\Security\ServiceSignature;

class ServiceParams {
    /**
     * @var string
     */
    private $token;

    /**
     * @var array
     */
    private $params;

    private function __construct() {
    }

    /**
     * @param array $params
     * @return ServiceParams
     */
    public static function fromArray(array $params) {
        $instance = new self();
        if (isset($params['token'])) {
            $instance->token = $params['token'];
            unset($params['token']);
        }
        $instance->params = $params;
        return $instance;
    }

    /**
     * @param ServiceSignature $signature
     * @return ServiceParams
     */
    public function sign(ServiceSignature $signature) {
        $new = new self();
        $new->token = $this->makeToken($signature);
        $new->params = $this->params;
        return $new;
    }

    /**
     * @param ServiceSignature $signature
     * @return bool
     */
    public function verify(ServiceSignature $signature) {
        if (!isset($this->token)) {
            return false;
        }
        return $this->token == $this->makeToken($signature);
    }

    /**
     * @return mixed
     */
    public function toArray() {
        $params = $this->params;
        if ($this->token) {
            $params['token'] = $this->token;
        }
        return $params;
    }

    public function serialize() {
        return JSON::encode($this->toArray());
    }

    private function makeToken(ServiceSignature $signature) {
        $params = $this->params;
        if (isset($params['cacheMarker'])) {
            unset($params['cacheMarker']);
        }
        ksort($params);
        array_walk($params, function (&$item) {
            $item = (string) $item;
        });
        return $signature->sign(json_encode($params));
    }

    public function replaceByTokenRef(TokenRefMaker $maker) {
        if (!isset($this->token)) {
            return $this;
        }
        $ref = $maker->getRef($this->token, $this->toArray());
        return $ref ? ServiceParams::fromArray(['ref' => $ref]) : $this;
    }
}
