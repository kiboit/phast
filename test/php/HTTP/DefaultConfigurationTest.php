<?php
namespace Kibo\Phast\HTTP;

use Kibo\Phast\Environment\Configuration;
use Kibo\Phast\ValueObjects\URL;

class DefaultConfigurationTest extends \PHPUnit\Framework\TestCase {
    public function testScriptWhitelistDoesNotUseRequestHost() {
        $originalHost = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;
        $_SERVER['HTTP_HOST'] = 'attacker.example';

        try {
            $config = Configuration::fromDefaults()->toArray();
        } finally {
            if ($originalHost === null) {
                unset($_SERVER['HTTP_HOST']);
            } else {
                $_SERVER['HTTP_HOST'] = $originalHost;
            }
        }

        $this->assertSame([], $config['scripts']['whitelist']);
    }

    public function testRetrieveURL() {
        $clientFactory = new ClientFactory();
        $config = Configuration::fromDefaults()->toArray();
        $client = $clientFactory->make($config);
        $url = URL::fromString('https://1.1.1.1/');
        if (!function_exists('curl_init')) {
            $this->expectException(Exceptions\NetworkError::class);
        }
        $response = $client->get($url);
        $this->assertStringContainsString('<html', $response->getContent());
    }
}
