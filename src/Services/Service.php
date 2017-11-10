<?php

namespace Kibo\Phast\Services;

use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\Exceptions\UnauthorizedException;
use Kibo\Phast\HTTP\Request;
use Kibo\Phast\HTTP\Response;
use Kibo\Phast\Security\ServiceSignature;

abstract class Service {

    /**
     * @var ServiceSignature
     */
    protected $signature;

    /**
     * @var string[]
     */
    protected $whitelist = [];

    /**
     * @param array $request
     * @return Response
     */
    abstract protected function handle(array $request);

    protected function getParams(Request $request) {
        return $request->getGet();
    }

    /**
     * Service constructor.
     *
     * @param ServiceSignature $signature
     * @param string[] $whitelist
     */
    public function __construct(ServiceSignature $signature, array $whitelist) {
        $this->signature = $signature;
        $this->whitelist = $whitelist;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function serve(Request $request) {
        $this->validateRequest($request->getGet());
        return $this->handle($this->getParams($request));
    }

    protected  function validateRequest(array $request) {
        $this->validateIntegrity($request);
        $this->validateToken($request);
        $this->validateWhitelisted($request);
    }

    protected function validateIntegrity(array $request) {
        if (!isset ($request['src'])) {
            throw new ItemNotFoundException('No source is set!');
        }
    }

    protected function validateToken(array $request) {
        if (!isset ($request['token'])) {
            throw new UnauthorizedException();
        }
        $token = $request['token'];
        unset ($request['token']);
        if (isset ($request['service'])) {
            unset ($request['service']);
        }
        if (!$this->signature->verify($token, http_build_query($request))) {
            throw new UnauthorizedException();
        }
    }

    protected function validateWhitelisted(array $request) {
        foreach ($this->whitelist as $pattern) {
            if (preg_match($pattern, $request['src'])) {
                return;
            }
        }
        throw new UnauthorizedException('Not allowed url: ' . $request['src']);
    }
}
