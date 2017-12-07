<?php

namespace Kibo\Phast\Logging\LogReaders;

use Kibo\Phast\Logging\LogEntry;
use Kibo\Phast\Logging\LogWriters\JSONLFileLogWriter;
use PHPUnit\Framework\TestCase;

class JSONLFileLogReaderTest extends TestCase {

    public function testReading() {
        $dir = sys_get_temp_dir();
        $file = 'json-l-reader-test';
        $writer = new JSONLFileLogWriter($dir, $file);
        $writer->writeEntry(new LogEntry(1, 'm1', ['k1' => 'v1']));
        $writer->writeEntry(new LogEntry(2, 'm2', ['k2' => 'v2']));

        $reader = new JSONLFileLogReader($dir, $file);
        $actual = [];
        foreach ($reader->readEntries() as $entry) {
            $actual[] = $entry->toArray();
        }

        $expected = [
            [
                'level' => 1,
                'message' => 'm1',
                'context' => ['k1' => 'v1']
            ],
            [
                'level' => 2,
                'message' => 'm2',
                'context' => ['k2' => 'v2']
            ]
        ];

    }

}
