<?php

namespace Kibo\Phast\Logging\LogWriters\JSONLFile;

use Kibo\Phast\Logging\LogEntry;
use Kibo\Phast\Logging\LogLevel;
use PHPUnit\Framework\TestCase;

class WriterTest extends TestCase {
    /**
     * @dataProvider getWriteMessageTestData
     */
    public function testWriteMessage($suffix) {
        $dir = sys_get_temp_dir() . '/phast-test';

        $fileSuffix = preg_replace('/[^0-9A-Za-z_-]/', '', (string) $suffix);
        if (!empty($fileSuffix)) {
            $fileSuffix = '-' . $fileSuffix;
        }
        $filename = $dir . '/log' . $fileSuffix . '.jsonl';

        @unlink($filename);

        $writer = new Writer($dir, $suffix);
        $message = new LogEntry(LogLevel::DEBUG, 'The message', ['key' => 'value']);
        $writer->writeEntry($message);

        $written = file_get_contents($filename);
        $this->assertStringEndsWith("\n", $written);
        $decoded = json_decode(trim($written), true);
        $this->assertEquals(LogLevel::DEBUG, $decoded['level']);
        $this->assertEquals('The message', $decoded['message']);
        $this->assertEquals(['key' => 'value'], $decoded['context']);
    }

    public function getWriteMessageTestData() {
        return [[null], ['123'], ['../../123zxcZXC-_']];
    }
}
