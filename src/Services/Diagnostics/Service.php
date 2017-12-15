<?php


namespace Kibo\Phast\Services\Diagnostics;

use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\HTTP\Response;
use Kibo\Phast\Logging\LogEntry;
use Kibo\Phast\Logging\LogReaders\JSONLFile\Reader;
use Kibo\Phast\Services\ServiceRequest;

class Service  {

    private $logRoot;

    public function __construct($logRoot) {
        $this->logRoot = $logRoot;
    }

    public function serve(ServiceRequest $request) {
        $requestId = $request->getDocumentRequestId();
        if (!$requestId) {
            throw new ItemNotFoundException('Could not find specified request id');
        }
        $reader = new Reader($this->logRoot, $requestId);
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
