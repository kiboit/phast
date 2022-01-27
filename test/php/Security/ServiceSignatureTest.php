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

    private $generatedToken;

    public function setUp(): void {
        parent::setUp();
        $this->cache = $this->createMock(Cache::class);
        $this->cache->method('get')
            ->willReturnCallback(function ($key, callable $cb) {
                if (!isset($this->generatedToken)) {
                    $this->generatedToken = $cb();
                }
                return $this->generatedToken;
            });
        $this->signature = new ServiceSignature($this->cache);
    }

    public function tearDown(): void {
        parent::tearDown();
        $this->generatedToken = null;
    }

    public function testSignAndVerify() {
        $signature = $this->signature->sign('the-value');
        $this->assertTrue($this->signature->verify($signature, 'the-value'));
        $this->assertFalse($this->signature->verify('adsasd', 'the-value'));
    }

    public function testDifferentValuesWhenTokenIsSet() {
        $autoToken = $this->signature->sign('the-value');
        $this->assertTrue($this->signature->verify($autoToken, 'the-value'));

        $this->signature->setIdentities('token');
        $setToken = $this->signature->sign('the-value');

        $this->assertNotSame($autoToken, $setToken);
        $this->assertFalse($this->signature->verify($autoToken, 'the-value'));
        $this->assertTrue($this->signature->verify($setToken, 'the-value'));
    }

    public function testAutoGenerationOfToken() {
        $signed = $this->signature->sign('the-value');
        $this->assertTrue($this->signature->verify($signed, 'the-value'));
        $this->assertEquals(ServiceSignature::AUTO_TOKEN_SIZE, strlen($this->generatedToken));
        for ($i = 0; $i < ServiceSignature::AUTO_TOKEN_SIZE; $i++) {
            $this->assertGreaterThan(32, ord($this->generatedToken[$i]));
            $this->assertLessThan(127, ord($this->generatedToken[$i]));
        }
    }

    public function testPrependingUserToToken() {
        $identities = ['the-user' => 'the-token', 'admin' => 'pass'];
        $this->signature->setIdentities($identities);
        $signature = $this->signature->sign('some-value');
        $this->assertStringStartsWith('the-user', $signature);
    }

    public function testVerificationWithLookup() {
        $identities = ['admin' => 'adminpass'];
        $this->signature->setIdentities($identities);
        $signature = $this->signature->sign('something-to-sign');

        $this->setUp();
        $identities = array_merge(['user1' => 'pass1'], $identities, ['user2' => 'pass2']);
        $this->signature->setIdentities($identities);
        $this->assertTrue($this->signature->verify($signature, 'something-to-sign'));

        $this->setUp();
        $this->signature->setIdentities(['user3' => 'pass2']);
        $this->assertFalse($this->signature->verify($signature, 'something-to-sign'));
    }

    public function testUsingCachedValueForVerificationWhenNonSet() {
        $signature = $this->signature->sign('something');
        $this->setUp();
        $this->assertTrue($this->signature->verify($signature, 'something'));
    }

    public function testDifferentCacheSaltForDifferentIdentities() {
        $this->signature->setIdentities('key');
        $salt1 = $this->signature->getCacheSalt();
        $this->signature->setIdentities(['user' => 'key1']);
        $salt2 = $this->signature->getCacheSalt();
        $this->signature->setIdentities(['user2' => 'key2']);
        $salt3 = $this->signature->getCacheSalt();

        $this->assertNotEquals($salt1, $salt2);
        $this->assertNotEquals($salt2, $salt3);
    }
}
