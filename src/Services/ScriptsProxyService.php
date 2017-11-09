<?php

namespace Kibo\Phast\Services;

use JSMin\JSMin;
use Kibo\Phast\Cache\Cache;
use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\Exceptions\UnauthorizedException;
use Kibo\Phast\HTTP\Response;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\ValueObjects\URL;

class ScriptsProxyService extends Service {

    /**
     * @var Retriever
     */
    private $retriever;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var string[]
     */
    private $allowedPatterns;

    /**
     * ScriptsProxyService constructor.
     *
     * @param Retriever $retriever
     * @param Cache $cache
     * @param string[] $allowedPatterns
     */
    public function __construct(Retriever $retriever, Cache $cache, array $allowedPatterns) {
        $this->retriever = $retriever;
        $this->cache = $cache;
        $this->allowedPatterns = $allowedPatterns;
    }

    protected function handle(array $request) {
        $cacheKey = $request['src'] . $request['cacheMarker'];
        $result =  $this->cache->get($cacheKey, function () use ($request) {
            $result = $this->retriever->retrieve(URL::fromString($request['src']));
            if ($result === false) {
                throw new ItemNotFoundException("Could not get {$request['src']}!");
            }
            return JSMin::minify($result);
        });

        $response = new Response();
        $response->setHeader('Content-Length', strlen($result));
        $response->setHeader('Cache-Control', 'max-age=' . (86400 * 365));
        $response->setHeader('Content-Type', 'application/javascript');
        $response->setContent($result);

        return $response;
    }

    protected function validateRequest(array $request) {
        foreach ($this->allowedPatterns as $pattern) {
            if (preg_match($pattern, $request['src'])) {
                return true;
            }
        }
        throw new UnauthorizedException('Not allowed url: ' . $request['src']);
    }

}
