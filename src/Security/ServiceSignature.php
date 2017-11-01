<?php

namespace Kibo\Phast\Security;

class ServiceSignature {

    /**
     * @var string
     */
    private $securityToken;

    /**
     * ImagesOptimizationSignature constructor.
     *
     * @param string $securityToken
     */
    public function __construct($securityToken) {
        $this->securityToken = $securityToken;
    }

    public function sign($value) {
        return substr(base_convert(md5($this->securityToken . $value), 16, 36), 0, 16);
    }

    public function verify($signature, $value) {
        return $signature === $this->sign($value);
    }

}
