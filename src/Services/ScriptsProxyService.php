<?php

namespace Kibo\Phast\Services;

use JSMin\JSMin;
use Kibo\Phast\Cache\Cache;
use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\HTTP\Response;

class ScriptsProxyService extends Service {

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var ObjectifiedFunctions
     */
    private $functions;

    /**
     * ScriptsProxyService constructor.
     *
     * @param Cache $cache
     * @param ObjectifiedFunctions|null $functions
     */
    public function __construct(Cache $cache, ObjectifiedFunctions $functions = null) {
        $this->cache = $cache;
        $this->functions = is_null($functions) ? new ObjectifiedFunctions() : $functions;
    }

    protected function handle(array $request) {
        $cacheKey = $request['src'] . $request['cacheMarker'];
        $result =  $this->cache->get($cacheKey, function () use ($request) {
            $result = @$this->functions->file_get_contents($request['src']);
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
        return true;
    }

}
