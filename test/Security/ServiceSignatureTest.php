<?php

namespace Kibo\Phast\Security;

use PHPUnit\Framework\TestCase;

class ServiceSignatureTest extends TestCase {

    /**
     * @var ServiceSignature
     */
    private $signature;

    public function setUp() {
        parent::setUp();
        $this->signature = new ServiceSignature('the-token');
    }

    public function testSignAndVerify() {
        $signature = $this->signature->sign('the-value');
        $this->assertTrue($this->signature->verify($signature, 'the-value'));
        $this->assertFalse($this->signature->verify('adsasd', 'the-value'));
    }

}
