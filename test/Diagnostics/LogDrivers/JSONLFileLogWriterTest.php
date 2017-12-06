<?php

namespace Kibo\Phast\Diagnostics\LogDrivers;


use PHPUnit\Framework\TestCase;
use Kibo\Phast\Diagnostics\LogEntry;
use Kibo\Phast\Diagnostics\LogLevel;

class JSONLFileLogWriterTest extends TestCase {

    public function testWriteMessage() {
        $filename = tempnam(sys_get_temp_dir(), 'XXX');
        $writer = new JSONLFileLogWriter($filename);
        $message = new LogEntry(LogLevel::DEBUG, 'The message', ['key' => 'value']);
        $writer->writeEntry($message);
        $written = file_get_contents($filename);
        $this->assertStringEndsWith("\n", $written);
        $decoded = json_decode(trim($written), true);
        $this->assertEquals(LogLevel::DEBUG, $decoded['level']);
        $this->assertEquals('The message', $decoded['message']);
        $this->assertEquals(['key' => 'value'], $decoded['context']);
    }

}
