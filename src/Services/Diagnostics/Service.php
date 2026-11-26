<?php


namespace Kibo\Phast\Services\Diagnostics;

use Kibo\Phast\Common\JSON;
use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\HTTP\Response;
use Kibo\Phast\Logging\LogReaders\JSONLFile\Reader;
use Kibo\Phast\Services\ServiceRequest;

/**
 * Serves the request log for the diagnostics overlay that the Diagnostics
 * HTML filter adds to documents when the diagnostics switch is on.
 */
class Service {
    private $logRoot;

    public function __construct($logRoot) {
        $this->logRoot = $logRoot;
    }

    public function serve(ServiceRequest $request) {
        $params = $request->getParams();
        if ($this->logRoot === null) {
            throw new ItemNotFoundException('Diagnostics are not enabled');
        }
        if (!isset($params['documentRequestId']) || !is_string($params['documentRequestId'])) {
            throw new ItemNotFoundException('No document request ID');
        }

        $items = iterator_to_array(
            (new Reader($this->logRoot, $params['documentRequestId']))->readEntries(),
            false
        );

        $response = new Response();
        $response->setContent(JSON::prettyEncode($items));
        $response->setHeader('Content-Type', 'application/json');
        return $response;
    }
}
