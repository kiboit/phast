<?php

namespace Kibo\Phast\Services;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\Exceptions\UnauthorizedException;
use Kibo\Phast\HTTP\Response;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\ValueObjects\URL;

class ProxyService extends Service {

    /**
     * @var Retriever
     */
    private $retriever;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * ScriptsProxyService constructor.
     *
     * @param ServiceSignature $signature
     * @param string[] $whitelist
     * @param Retriever $retriever
     */
    public function __construct(ServiceSignature $signature, array $whitelist, Retriever $retriever) {
        parent::__construct($signature, $whitelist);
        $this->retriever = $retriever;
    }

    protected function handle(array $request) {
        $result = $this->doRequest($request);

        $response = new Response();
        $response->setHeader('Content-Length', strlen($result));
        $response->setHeader('Cache-Control', 'max-age=' . (86400 * 365));
        $response->setContent($result);

        return $response;
    }

    protected function doRequest(array $request) {
        $result = $this->retriever->retrieve(URL::fromString($request['src']));
        if ($result === false) {
            throw new ItemNotFoundException("Could not get {$request['src']}!");
        }
        return $result;
    }

    protected function validateRequest(array $request) {
        $this->validateIntegrity($request);
        try {
            $this->validateToken($request);
        } catch (UnauthorizedException $e) {
            $this->validateWhitelisted($request);
        }
    }

}
