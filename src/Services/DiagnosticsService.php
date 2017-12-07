<?php


namespace Kibo\Phast\Services;

use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\HTTP\Response;
use Kibo\Phast\Logging\LogEntry;
use Kibo\Phast\Logging\LogReaders\JSONLFileLogReader;

class DiagnosticsService  {

    private $logRoot;

    public function __construct($logRoot) {
        $this->logRoot = $logRoot;
    }

    public function serve(ServiceRequest $request) {
        $requestId = $request->getRequestId();
        if (!$requestId) {
            throw new ItemNotFoundException('Could not find specified request id');
        }
        $reader = new JSONLFileLogReader($this->logRoot, $requestId);
        $entries = [];
        /** @var LogEntry $entry */
        foreach ($reader->readEntries() as $entry) {
            $entries[] = $entry->toArray();
        }
        $response = new Response();
        $response->setContent(json_encode($entries));
        $response->setHeader('Content-Type', 'application/json');
        return $response;
    }


}
