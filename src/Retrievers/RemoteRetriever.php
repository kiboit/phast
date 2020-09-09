<?php

namespace Kibo\Phast\Retrievers;

use Kibo\Phast\HTTP\Client;
use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\ValueObjects\URL;

class RemoteRetriever implements Retriever {
    use DynamicCacheSaltTrait;
    use LoggingTrait;

    private $client;

    public function __construct(Client $client) {
        $this->client = $client;
    }

    public function retrieve(URL $url) {
        $cdnLoop = ['Phast'];
        if (!empty($_SERVER['HTTP_CDN_LOOP'])) {
            $cdnLoop[] = $_SERVER['HTTP_CDN_LOOP'];
        }
        try {
            $response = $this->client->get($url, [
                'User-Agent' => 'Mozilla/5.0 (X11; Fedora; Linux x86_64; rv:56.0) Gecko/20100101 Firefox/56.0',
                'CDN-Loop' => implode(', ', $cdnLoop),
            ]);
        } catch (\Exception $e) {
            $this->logger()->warning('Caught {cls} while fetching {url}: ({code}) {message}', [
                'cls' => get_class($e),
                'url' => (string) $url,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
            return false;
        }
        return $response->getContent();
    }
}
