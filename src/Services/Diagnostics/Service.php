<?php


namespace Kibo\Phast\Services\Diagnostics;

use Kibo\Phast\Diagnostics\SystemDiagnostics;
use Kibo\Phast\HTTP\Response;
use Kibo\Phast\Logging\LogReaders\JSONLFile\Reader;
use Kibo\Phast\Services\ServiceRequest;

class Service  {

    private $logRoot;

    public function __construct($logRoot) {
        $this->logRoot = $logRoot;
    }

    public function serve(ServiceRequest $request) {
        $params = $request->getParams();
        if (isset ($params['documentRequestId'])) {
            $items = $this->getRequestLog($params['documentRequestId']);
        } else {
            $items = $this->getSystemDiagnostics();
        }

        $response = new Response();
        $response->setContent(json_encode($items, JSON_PRETTY_PRINT));
        $response->setHeader('Content-Type', 'application/json');
        return $response;
    }

    private function getRequestLog($requestId) {
        return iterator_to_array((new Reader($this->logRoot, $requestId))->readEntries());
    }

    private function getSystemDiagnostics() {
        return (new SystemDiagnostics())->run(require PHAST_CONFIG_FILE);
    }


}
