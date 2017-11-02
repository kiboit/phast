<?php

namespace Kibo\Phast\Security;

use Kibo\Phast\Cache\Cache;
use PHPUnit\Framework\TestCase;

class ServiceSignatureTest extends TestCase {

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $cache;

    /**
     * @var ServiceSignature
     */
    private $signature;

    public function setUp() {
        parent::setUp();
        $this->cache = $this->createMock(Cache::class);
        $this->signature = new ServiceSignature($this->cache);
    }

    public function testSignAndVerify() {
        $signature = $this->signature->sign('the-value');
        $this->assertTrue($this->signature->verify($signature, 'the-value'));
        $this->assertFalse($this->signature->verify('adsasd', 'the-value'));
    }

    public function testDifferentValuesWhenTokenIsSet() {
        $autoToken = $this->signature->sign('the-value');
        $this->assertTrue($this->signature->verify($autoToken, 'the-value'));

        $this->signature->setSecurityToken('token');
        $setToken = $this->signature->sign('the-value');

        $this->assertNotSame($autoToken, $setToken);
        $this->assertFalse($this->signature->verify($autoToken, 'the-value'));
        $this->assertTrue($this->signature->verify($setToken, 'the-value'));
    }

    public function testAutoGenerationOfToken() {
        $token = null;
        $this->cache->expects($this->once())
            ->method('get')
            ->willReturnCallback(function ($key, callable $cb) use (&$token) {
                $token = $cb();
                return $token;
            });
        $signed = $this->signature->sign('the-value');
        $this->assertTrue($this->signature->verify($signed, 'the-value'));
        $this->assertEquals(ServiceSignature::AUTO_TOKEN_SIZE, strlen($token));
        for ($i = 0; $i < ServiceSignature::AUTO_TOKEN_SIZE; $i++) {
            $this->assertGreaterThan(32, ord($token[$i]));
            $this->assertLessThan(127, ord($token[$i]));
        }
    }

}
