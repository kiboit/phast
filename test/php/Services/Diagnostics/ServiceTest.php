<?php
namespace Kibo\Phast\Services\Diagnostics;

use Kibo\Phast\Environment\Configuration;
use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\HTTP\Request;
use Kibo\Phast\Logging\LogEntry;
use Kibo\Phast\Logging\LogLevel;
use Kibo\Phast\Logging\LogWriters\JSONLFile\Writer;
use Kibo\Phast\Services\ServiceRequest;
use PHPUnit\Framework\TestCase;

class ServiceTest extends TestCase {
    private $logRoot;

    public function setUp(): void {
        parent::setUp();
        ServiceRequest::resetRequestState();
        $this->logRoot = sys_get_temp_dir() . '/phast-diagnostics-test-' . getmypid();
    }

    public function tearDown(): void {
        foreach (glob($this->logRoot . '/*') ?: [] as $file) {
            unlink($file);
        }
        @rmdir($this->logRoot);
        ServiceRequest::resetRequestState();
        parent::tearDown();
    }

    public function testServesRequestLog() {
        $writer = new Writer($this->logRoot, '12345');
        $writer->writeEntry(new LogEntry(LogLevel::INFO, 'Hello {name}', ['name' => 'world']));

        $response = $this->makeService(true)->serve($this->makeRequest('diagnostics,-phast', '12345'));

        $this->assertSame('application/json', $response->getHeader('Content-Type'));
        $entries = json_decode($response->getContent(), true);
        $this->assertCount(1, $entries);
        $this->assertSame('Hello {name}', $entries[0]['message']);
        $this->assertSame(['name' => 'world'], $entries[0]['context']);
    }

    public function testNotFoundWithoutDocumentRequestId() {
        $this->expectException(ItemNotFoundException::class);
        $this->makeService(true)->serve($this->makeRequest('diagnostics', null));
    }

    public function testNotFoundWhenDiagnosticsAreOff() {
        $this->expectException(ItemNotFoundException::class);
        $this->makeService(false)->serve($this->makeRequest(null, '12345'));
    }

    private function makeService($diagnostics) {
        $config = Configuration::fromDefaults()->withUserConfiguration(new Configuration([
            'logging' => ['logWriters' => [1 => ['logRoot' => $this->logRoot]]],
            'switches' => ['diagnostics' => $diagnostics],
        ]))->getRuntimeConfig()->toArray();
        return (new Factory())->make($config);
    }

    private function makeRequest($switches, $documentRequestId) {
        $query = ['service' => 'diagnostics'];
        if ($switches !== null) {
            $query['phast'] = $switches;
        }
        if ($documentRequestId !== null) {
            $query['documentRequestId'] = $documentRequestId;
        }
        return ServiceRequest::fromHTTPRequest(
            Request::fromArray($query, ['REQUEST_URI' => '/phast.php?' . http_build_query($query)])
        );
    }
}
