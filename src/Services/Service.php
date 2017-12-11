<?php

namespace Kibo\Phast\Services;

use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\Exceptions\UnauthorizedException;
use Kibo\Phast\HTTP\Response;
use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\Security\ServiceSignature;

abstract class Service {
    use LoggingTrait;

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

    protected function getParams(ServiceRequest $request) {
        return $request->getParams();
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
     * @param ServiceRequest $request
     * @return Response
     */
    public function serve(ServiceRequest $request) {
        $this->validateRequest($request);
        return $this->handle($this->getParams($request));
    }

    protected  function validateRequest(ServiceRequest $request) {
        $this->validateIntegrity($request);
        $this->validateToken($request);
        $this->validateWhitelisted($request);
    }

    protected function validateIntegrity(ServiceRequest $request) {
        $params = $request->getParams();
        if (!isset ($params['src'])) {
            throw new ItemNotFoundException('No source is set!');
        }
    }

    protected function validateToken(ServiceRequest $request) {
        if (!$request->verify($this->signature)) {
            throw new UnauthorizedException('Invalid token');
        }
    }

    protected function validateWhitelisted(ServiceRequest $request) {
        $params = $request->getParams();
        foreach ($this->whitelist as $pattern) {
            if (preg_match($pattern, $params['src'])) {
                return;
            }
        }
        throw new UnauthorizedException('Not allowed url: ' . $params['src']);
    }
}
