<?php

namespace Kibo\Phast\Security;

use PHPUnit\Framework\TestCase;

class ImagesOptimizationSignatureTest extends TestCase {

    /**
     * @var ImagesOptimizationSignature
     */
    private $signature;

    public function setUp() {
        parent::setUp();
        $this->signature = new ImagesOptimizationSignature('the-token');
    }

    public function testSignAndVerify() {
        $signature = $this->signature->sign('the-value');
        $this->assertTrue($this->signature->verify($signature, 'the-value'));
        $this->assertFalse($this->signature->verify('adsasd', 'the-value'));
    }

}
