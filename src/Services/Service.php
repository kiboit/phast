<?php

namespace Kibo\Phast\Services;

use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\Exceptions\UnauthorizedException;
use Kibo\Phast\HTTP\Request;
use Kibo\Phast\Security\ServiceSignature;

abstract class Service {

    /**
     * @var ServiceSignature
     */
    protected $signature;

    abstract protected function handle(array $request);

    protected function getParams(Request $request) {
        return $request->getGet();
    }

    public function serve(Request $request) {
        $this->validateRequest($request->getGet());
        return $this->handle($this->getParams($request));
    }

    private function validateRequest(array $request) {
        if (!isset ($request['src'])) {
            throw new ItemNotFoundException('No source is set!');
        }
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
}
