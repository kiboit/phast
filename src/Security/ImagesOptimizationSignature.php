<?php

namespace Kibo\Phast\Security;

class ImagesOptimizationSignature {

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
        return sha1($this->securityToken . $value);
    }

    public function verify($signature, $value) {
        return $signature === $this->sign($value);
    }

}
