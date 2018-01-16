<?php

namespace Kibo\Phast\Services\Scripts;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\Common\JSMinifier;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\Services\ProxyBaseService;

class Service extends ProxyBaseService {

    /**
     * @var bool
     */
    private $removeLicenseHeaders = true;

    /**
     * @var Cache
     */
    private $cache;

    public function __construct(
        ServiceSignature $signature,
        $whitelist,
        Retriever $retriever,
        $removeLicenseHeaders,
        Cache $cache
    ) {
        parent::__construct($signature, $whitelist, $retriever);
        $this->removeLicenseHeaders = $removeLicenseHeaders;
        $this->cache = $cache;
    }

    protected function handle(array $request) {
        $response = parent::handle($request);
        $response->setHeader('Content-Type', 'application/javascript');
        return $response;
    }

    protected function doRequest(array $request) {
        $result = parent::doRequest($request);
        $cacheKey = md5($result);
        return $this->cache->get($cacheKey, function () use ($result) {
            return (new JSMinifier($result, $this->removeLicenseHeaders))->min();
        });
    }

}
