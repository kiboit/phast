<?php

namespace Kibo\Phast\Services;

use Kibo\Phast\Exceptions\UnauthorizedException;

class ProxyBaseService extends BaseService {
    
    protected function getParams(ServiceRequest $request) {
        $params = parent::getParams($request);
        $compression = $request->getHTTPRequest()->getHeader('Accept-Encoding');
        if ($compression) {
            $matches = [];
            preg_match_all('/([a-z*]+)(?:;q=[\d.]*)?(?:,\s*|$)/i', $compression, $matches);
            $params['accept-encoding'] = $matches[1];
        }
        return $params;
    }

    protected function validateRequest(ServiceRequest $request) {
        $this->validateIntegrity($request);
        try {
            $this->validateToken($request);
            $this->logger()->info('Token OK');
        } catch (UnauthorizedException $e) {
            $this->logger()->info('Token not OK. Validating whitelist.');
            $this->validateWhitelisted($request);
            $this->logger()->info('Whitelisted!');
        }
    }

}
